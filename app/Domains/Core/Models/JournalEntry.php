<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\JournalImmutableException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_id',
        'account_id',
        'description',
        // debit and credit are deliberately excluded from fillable to force Service layer explicit assignment
    ];

    protected $casts = [
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::updating(function (JournalEntry $entry) {
            throw new JournalImmutableException('Journal entries are immutable.');
        });

        static::deleting(function (JournalEntry $entry) {
            throw new JournalImmutableException('Journal entries cannot be deleted.');
        });
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
