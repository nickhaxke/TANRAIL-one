<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'unit_id',
        'tax_category_id',
        'item_name_snapshot',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_rate_snapshot',
        'tax_amount',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_rate_snapshot' => 'decimal:2',
        'tax_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::saving(function (InvoiceLine $line) {
            if ($line->invoice_id && $line->item_id) {
                $invoice = Invoice::withoutGlobalScopes()->find($line->invoice_id);
                $item = Item::withoutGlobalScopes()->find($line->item_id);

                if ($invoice && $item) {
                    $itemBu = BusinessUnit::withoutGlobalScopes()->find($item->business_unit_id);
                    if ($itemBu && (int) $itemBu->organization_id !== (int) $invoice->organization_id) {
                        throw new CrossOrganizationException("Item {$line->item_id} does not belong to Organization {$invoice->organization_id}.");
                    }
                }
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class)->withoutGlobalScopes();
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }
}
