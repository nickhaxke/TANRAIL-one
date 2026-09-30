<?php

namespace App\Domains\Modules\EventManagement\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use App\Domains\Modules\EventManagement\Enums\EventBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventBooking extends Model
{
    use HasFactory;

    protected $table = 'event_bookings';

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'branch_id',
        'customer_id',
        'booking_number',
        'event_name',
        'event_type',
        'venue_location',
        'start_date_time',
        'end_date_time',
        'guest_count',
        'subtotal',
        'tax_amount',
        'total_amount',
        'deposit_required_amount',
        'deposit_paid_amount',
        'status',
        'deposit_invoice_id',
        'final_invoice_id',
        'notes',
        'created_by',
        'confirmed_by',
        'completed_by',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'guest_count' => 'integer',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'deposit_required_amount' => 'decimal:4',
        'deposit_paid_amount' => 'decimal:4',
        'status' => EventBookingStatus::class,
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (EventBooking $booking) {
            if ($booking->customer_id) {
                $customer = Customer::withoutGlobalScopes()->find($booking->customer_id);
                if ($customer && (int) $customer->organization_id !== (int) $booking->organization_id) {
                    throw new CrossOrganizationException(
                        "Customer ID {$booking->customer_id} belongs to Organization {$customer->organization_id}, expected {$booking->organization_id}."
                    );
                }
            }

            if ($booking->branch_id) {
                $branch = Branch::find($booking->branch_id);
                if ($branch && (int) $branch->business_unit_id !== (int) $booking->business_unit_id) {
                    throw new CrossOrganizationException(
                        "Branch ID {$booking->branch_id} belongs to Business Unit {$branch->business_unit_id}, expected {$booking->business_unit_id}."
                    );
                }
            }
        });

        static::updating(function (EventBooking $booking) {
            $originalStatus = $booking->getOriginal('status');
            if ($originalStatus instanceof EventBookingStatus) {
                $originalStatus = $originalStatus->value;
            }

            if (in_array($originalStatus, [EventBookingStatus::COMPLETED->value, EventBookingStatus::CANCELLED->value])) {
                throw new InvalidStateTransitionException(
                    "Event booking {$booking->booking_number} is in terminal status '{$originalStatus}' and cannot be modified."
                );
            }
        });

        static::deleting(function (EventBooking $booking) {
            $status = $booking->status instanceof EventBookingStatus ? $booking->status->value : $booking->status;
            if (in_array($status, [EventBookingStatus::COMPLETED->value, EventBookingStatus::CONFIRMED->value])) {
                throw new InvalidStateTransitionException(
                    "Finalized or active event booking {$booking->booking_number} in status '{$status}' cannot be deleted."
                );
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

    public function depositInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'deposit_invoice_id');
    }

    public function finalInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'final_invoice_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(EventBookingPackage::class, 'event_booking_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventBookingAttendee::class, 'event_booking_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
