<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\JournalStatus;
use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\FinancialConfigurationException;
use App\Domains\Core\Exceptions\FiscalPeriodClosedException;
use App\Domains\Core\Exceptions\JournalImbalanceException;
use App\Domains\Core\Models\Account;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FiscalPeriod;
use App\Domains\Core\Models\Journal;
use App\Domains\Core\Models\JournalEntry;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * @param  array  $lines  Array of associative arrays: [['account_id' => 1, 'debit' => 100, 'credit' => 0, 'description' => '...'], ...]
     *
     * @throws FiscalPeriodClosedException
     * @throws JournalImbalanceException
     * @throws CrossOrganizationException
     * @throws FinancialConfigurationException
     */
    public function postJournal(
        BusinessUnit $businessUnit,
        string $description,
        Carbon $postingDate,
        array $lines,
        ?Model $reference = null,
        ?int $userId = null,
        JournalType $type = JournalType::OPERATIONAL
    ): Journal {
        if (empty($lines)) {
            throw new FinancialConfigurationException('Cannot post an empty journal.');
        }

        $this->validatePeriodOpen($businessUnit->organization_id, $postingDate);

        $totalDebit = '0.0000';
        $totalCredit = '0.0000';

        foreach ($lines as $line) {
            $debit = bcadd((string) ($line['debit'] ?? '0'), '0', 4);
            $credit = bcadd((string) ($line['credit'] ?? '0'), '0', 4);

            if (bccomp($debit, '0', 4) < 0 || bccomp($credit, '0', 4) < 0) {
                throw new FinancialConfigurationException('Debits and Credits must be non-negative values.');
            }

            if (bccomp($debit, '0', 4) > 0 && bccomp($credit, '0', 4) > 0) {
                throw new FinancialConfigurationException('A single journal line cannot have both a debit and a credit.');
            }

            $totalDebit = bcadd($totalDebit, $debit, 4);
            $totalCredit = bcadd($totalCredit, $credit, 4);

            $account = Account::withoutGlobalScopes()->find($line['account_id']);
            if (! $account) {
                throw new FinancialConfigurationException("Account {$line['account_id']} not found.");
            }
            if ($account->organization_id !== $businessUnit->organization_id) {
                throw new CrossOrganizationException("Account {$account->id} does not belong to Organization {$businessUnit->organization_id}.");
            }
            if (! $account->is_active) {
                throw new FinancialConfigurationException("Account {$account->id} is not active.");
            }
        }

        if (bccomp($totalDebit, $totalCredit, 4) !== 0) {
            throw new JournalImbalanceException("Journal does not balance. Total Debits: {$totalDebit}, Total Credits: {$totalCredit}");
        }

        return DB::transaction(function () use ($businessUnit, $description, $postingDate, $lines, $reference, $userId, $type) {
            $journal = new Journal;
            $journal->forceFill([
                'business_unit_id' => $businessUnit->id,
                'type' => $type,
                'description' => $description,
                'posting_date' => $postingDate,
                'status' => JournalStatus::POSTED,
                'reverses_journal_id' => null,
                'created_by' => $userId,
            ]);

            if ($reference) {
                $journal->reference()->associate($reference);
            }

            $journal->save();

            foreach ($lines as $line) {
                $entry = new JournalEntry;
                $entry->forceFill([
                    'journal_id' => $journal->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ])->save();
            }

            return $journal;
        });
    }

    public function reverseJournal(Journal $originalJournal, string $reversalDescription, ?int $userId = null): Journal
    {
        if ($originalJournal->status === JournalStatus::REVERSED) {
            throw new Exception('Journal is already reversed.');
        }

        $postingDate = now();
        $this->validatePeriodOpen($originalJournal->businessUnit->organization_id, $postingDate);

        return DB::transaction(function () use ($originalJournal, $reversalDescription, $postingDate, $userId) {
            // Lock the original journal to prevent concurrent reversals
            $lockedJournal = Journal::withoutGlobalScopes()->where('id', $originalJournal->id)->lockForUpdate()->first();

            if ($lockedJournal->status === JournalStatus::REVERSED) {
                throw new Exception('Journal was reversed concurrently.');
            }

            $lockedJournal->forceFill(['status' => JournalStatus::REVERSED])->save();
            $originalJournal->forceFill(['status' => JournalStatus::REVERSED]); // memory sync

            $reversalJournal = new Journal;
            $reversalJournal->forceFill([
                'business_unit_id' => $lockedJournal->business_unit_id,
                'type' => JournalType::REVERSAL,
                'description' => $reversalDescription,
                'posting_date' => $postingDate,
                'status' => JournalStatus::POSTED,
                'reverses_journal_id' => $lockedJournal->id,
                'created_by' => $userId,
            ]);

            if ($lockedJournal->reference_id) {
                $reversalJournal->forceFill([
                    'reference_type' => $lockedJournal->reference_type,
                    'reference_id' => $lockedJournal->reference_id,
                ]);
            }

            $reversalJournal->save();

            foreach ($lockedJournal->entries as $entry) {
                $reversalEntry = new JournalEntry;
                $reversalEntry->forceFill([
                    'journal_id' => $reversalJournal->id,
                    'account_id' => $entry->account_id,
                    // Swap debits and credits
                    'debit' => $entry->credit,
                    'credit' => $entry->debit,
                    'description' => 'Reversal of line: '.$entry->id,
                ])->save();
            }

            return $reversalJournal;
        });
    }

    private function validatePeriodOpen(int $organizationId, Carbon $date): void
    {
        $period = FiscalPeriod::withoutGlobalScopes()
            ->where('organization_id', $organizationId)
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->first();

        if (! $period) {
            $anyPeriod = FiscalPeriod::withoutGlobalScopes()
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $anyPeriod) {
                return;
            }

            throw new FiscalPeriodClosedException("No fiscal period found for date {$date->toDateString()}.");
        }

        if ($period->is_closed) {
            throw new FiscalPeriodClosedException("Fiscal period '{$period->name}' is closed. Cannot post journals into this period.");
        }
    }
}
