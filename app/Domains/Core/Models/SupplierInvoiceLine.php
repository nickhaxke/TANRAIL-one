<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_invoice_id',
        'item_id',
        'unit_id',
        'tax_category_id',
        'purchase_receipt_line_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate_snapshot',
        'tax_amount',
        'subtotal',
        'total',
        'received_quantity_reference',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'tax_rate_snapshot' => 'decimal:2',
        'tax_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
        'received_quantity_reference' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::saving(function (SupplierInvoiceLine $line) {
            if ($line->supplier_invoice_id && $line->item_id) {
                $invoice = SupplierInvoice::withoutGlobalScopes()->find($line->supplier_invoice_id);
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

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class)->withoutGlobalScopes();
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

    public function purchaseReceiptLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceiptLine::class);
    }
}
