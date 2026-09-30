<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\SupplierInvoiceMatchStatus;
use App\Domains\Core\Enums\SupplierInvoiceStatus;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'branch_id',
        'supplier_id',
        'purchase_order_id',
        'supplier_bill_number',
        'internal_reference',
        'bill_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total',
        'amount_paid',
        'balance_due',
        'status',
        'match_status',
        'discrepancy_reason',
        'match_details',
        'overridden_by',
        'overridden_at',
        'override_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'overridden_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
        'amount_paid' => 'decimal:4',
        'balance_due' => 'decimal:4',
        'status' => SupplierInvoiceStatus::class,
        'match_status' => SupplierInvoiceMatchStatus::class,
        'match_details' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (SupplierInvoice $invoice) {
            if ($invoice->supplier_id) {
                $supplier = Supplier::withoutGlobalScopes()->find($invoice->supplier_id);
                if ($supplier && (int) $supplier->organization_id !== (int) $invoice->organization_id) {
                    throw new CrossOrganizationException("Supplier {$invoice->supplier_id} does not belong to Organization {$invoice->organization_id}.");
                }
            }

            if ($invoice->branch_id) {
                $branch = Branch::withoutGlobalScopes()->find($invoice->branch_id);
                if ($branch && (int) $branch->business_unit_id !== (int) $invoice->business_unit_id) {
                    throw new CrossOrganizationException("Branch {$invoice->branch_id} does not belong to Business Unit {$invoice->business_unit_id}.");
                }
            }

            if ($invoice->purchase_order_id) {
                $po = PurchaseOrder::withoutGlobalScopes()->find($invoice->purchase_order_id);
                if ($po && (int) $po->business_unit_id !== (int) $invoice->business_unit_id) {
                    throw new CrossOrganizationException("Purchase Order {$invoice->purchase_order_id} does not belong to Business Unit {$invoice->business_unit_id}.");
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function overriddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'overridden_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SupplierInvoiceLine::class);
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'allocatable');
    }
}
