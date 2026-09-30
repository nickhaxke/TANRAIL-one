<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\InvoiceStatus;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'branch_id',
        'customer_id',
        'order_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'currency',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'amount_paid',
        'balance_due',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
        'amount_paid' => 'decimal:4',
        'balance_due' => 'decimal:4',
        'status' => InvoiceStatus::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (Invoice $invoice) {
            if ($invoice->customer_id) {
                $customer = Customer::withoutGlobalScopes()->find($invoice->customer_id);
                if ($customer && (int) $customer->organization_id !== (int) $invoice->organization_id) {
                    throw new CrossOrganizationException("Customer {$invoice->customer_id} does not belong to Organization {$invoice->organization_id}.");
                }
            }

            if ($invoice->branch_id) {
                $branch = Branch::withoutGlobalScopes()->find($invoice->branch_id);
                if ($branch && (int) $branch->business_unit_id !== (int) $invoice->business_unit_id) {
                    throw new CrossOrganizationException("Branch {$invoice->branch_id} does not belong to Business Unit {$invoice->business_unit_id}.");
                }
            }

            if ($invoice->order_id) {
                $order = Order::withoutGlobalScopes()->find($invoice->order_id);
                if ($order && (int) $order->business_unit_id !== (int) $invoice->business_unit_id) {
                    throw new CrossOrganizationException("Order {$invoice->order_id} does not belong to Business Unit {$invoice->business_unit_id}.");
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'allocatable');
    }
}
