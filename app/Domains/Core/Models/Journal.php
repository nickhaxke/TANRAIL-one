<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\JournalStatus;
use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Exceptions\JournalImmutableException;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'type',
        'reference_type',
        'reference_id',
        'description',
        'posting_date',
        'reverses_journal_id',
        'created_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'status' => JournalStatus::class,
        'type' => JournalType::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);

        static::updating(function (Journal $journal) {
            // Can only update if we are explicitly reversing it, and nothing else is dirty
            if ($journal->isDirty('status') && $journal->status === JournalStatus::REVERSED) {
                $dirty = array_keys($journal->getDirty());
                if ($dirty === ['status']) {
                    return;
                }
            }
            throw new JournalImmutableException('Journals are immutable once posted.');
        });

        static::deleting(function (Journal $journal) {
            throw new JournalImmutableException('Journals cannot be deleted.');
        });
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function reversesJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'reverses_journal_id');
    }
}
