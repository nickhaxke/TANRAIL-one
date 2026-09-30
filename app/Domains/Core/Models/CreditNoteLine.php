<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditNoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id',
        'item_id',
        'description',
        'quantity',
        'unit_price',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::saving(function (CreditNoteLine $line) {
            if ($line->credit_note_id && $line->item_id) {
                $note = CreditNote::withoutGlobalScopes()->find($line->credit_note_id);
                $item = Item::withoutGlobalScopes()->find($line->item_id);

                if ($note && $item) {
                    $itemBu = BusinessUnit::withoutGlobalScopes()->find($item->business_unit_id);
                    if ($itemBu && (int) $itemBu->organization_id !== (int) $note->organization_id) {
                        throw new CrossOrganizationException("Item {$line->item_id} does not belong to Organization {$note->organization_id}.");
                    }
                }
            }
        });
    }

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class)->withoutGlobalScopes();
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
