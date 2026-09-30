<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\CreditNoteStatus;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'branch_id',
        'customer_id',
        'invoice_id',
        'credit_note_number',
        'issue_date',
        'subtotal',
        'tax_amount',
        'total',
        'amount_allocated',
        'remaining_credit',
        'status',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
        'amount_allocated' => 'decimal:4',
        'remaining_credit' => 'decimal:4',
        'status' => CreditNoteStatus::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (CreditNote $note) {
            if ($note->customer_id) {
                $customer = Customer::withoutGlobalScopes()->find($note->customer_id);
                if ($customer && (int) $customer->organization_id !== (int) $note->organization_id) {
                    throw new CrossOrganizationException("Customer {$note->customer_id} does not belong to Organization {$note->organization_id}.");
                }
            }

            if ($note->branch_id) {
                $branch = Branch::withoutGlobalScopes()->find($note->branch_id);
                if ($branch && (int) $branch->business_unit_id !== (int) $note->business_unit_id) {
                    throw new CrossOrganizationException("Branch {$note->branch_id} does not belong to Business Unit {$note->business_unit_id}.");
                }
            }

            if ($note->invoice_id) {
                $invoice = Invoice::withoutGlobalScopes()->find($note->invoice_id);
                if ($invoice && (int) $invoice->business_unit_id !== (int) $note->business_unit_id) {
                    throw new CrossOrganizationException("Invoice {$note->invoice_id} does not belong to Business Unit {$note->business_unit_id}.");
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class)->withoutGlobalScopes();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(CreditNoteLine::class);
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'allocatable');
    }
}
