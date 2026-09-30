<?php

namespace App\Domains\Modules\EventManagement\Services;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Models\TaxCategory;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\InvoiceService;
use App\Domains\Modules\EventManagement\Enums\EventBookingStatus;
use App\Domains\Modules\EventManagement\Models\EventBooking;
use App\Domains\Modules\EventManagement\Models\EventBookingAttendee;
use App\Domains\Modules\EventManagement\Models\EventBookingPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EventBookingService
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function createBooking(
        Branch $branch,
        Customer $customer,
        string $eventName,
        Carbon|string $startDateTime,
        Carbon|string $endDateTime,
        int $guestCount,
        ?string $eventType = null,
        ?string $venueLocation = null,
        float|string $depositRequiredAmount = 0,
        ?string $bookingNumber = null,
        ?string $notes = null,
        ?User $user = null
    ): EventBooking {
        $bu = $branch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($branch->business_unit_id);
        if ((int) $customer->organization_id !== (int) $bu->organization_id) {
            throw new CrossOrganizationException("Customer {$customer->id} does not belong to Organization {$bu->organization_id}.");
        }
        // Validate customer's business unit if Customer model has it (optional check depending on Customer scope, assuming org-level for now)
        // Ensure the current user has access to the branch's BU
        if ($user && ! $user->hasPermissionTo('manage-events', BusinessUnit::class, $bu->id)) {
            throw new InvalidArgumentException("User does not have permission for Business Unit {$bu->id}");
        }

        if ($guestCount <= 0) {
            throw new InvalidArgumentException('Guest count must be greater than zero.');
        }

        $start = is_string($startDateTime) ? Carbon::parse($startDateTime) : $startDateTime;
        $end = is_string($endDateTime) ? Carbon::parse($endDateTime) : $endDateTime;

        if ($start->greaterThanOrEqualTo($end)) {
            throw new InvalidArgumentException('Start date/time must be strictly before end date/time.');
        }

        $depStr = bcadd((string) $depositRequiredAmount, '0', 4);
        if (bccomp($depStr, '0', 4) < 0) {
            throw new InvalidArgumentException('Deposit required amount cannot be negative.');
        }

        $number = $bookingNumber ?? ($branch->code.'-EVT-'.str_pad((string) (EventBooking::withoutGlobalScopes()->where('business_unit_id', $branch->business_unit_id)->count() + 1), 5, '0', STR_PAD_LEFT));

        $booking = new EventBooking;
        $booking->forceFill([
            'organization_id' => $bu->organization_id,
            'business_unit_id' => $branch->business_unit_id,
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'booking_number' => $number,
            'event_name' => $eventName,
            'event_type' => $eventType,
            'venue_location' => $venueLocation,
            'start_date_time' => $start->toDateTimeString(),
            'end_date_time' => $end->toDateTimeString(),
            'guest_count' => $guestCount,
            'subtotal' => '0.0000',
            'tax_amount' => '0.0000',
            'total_amount' => '0.0000',
            'deposit_required_amount' => $depStr,
            'deposit_paid_amount' => '0.0000',
            'status' => EventBookingStatus::DRAFT,
            'notes' => $notes,
            'created_by' => $user?->id,
        ]);
        $booking->save();

        return $booking;
    }

    public function addPackage(
        EventBooking $booking,
        Item $item,
        float|string $quantity,
        float|string|null $unitPrice = null,
        ?TaxCategory $taxCategory = null,
        ?string $notes = null
    ): EventBookingPackage {
        if (! in_array($booking->status, [EventBookingStatus::DRAFT, EventBookingStatus::QUOTED])) {
            throw new InvalidStateTransitionException("Cannot add packages to booking {$booking->booking_number} in status {$booking->status->value}.");
        }

        $qtyStr = bcadd((string) $quantity, '0', 4);
        if (bccomp($qtyStr, '0', 4) <= 0) {
            throw new InvalidArgumentException('Package quantity must be greater than zero.');
        }

        $itemBu = $item->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($item->business_unit_id);
        if (! $itemBu || (int) $itemBu->organization_id !== (int) $booking->organization_id) {
            $itemOrgId = $itemBu ? $itemBu->organization_id : 'Unknown';
            throw new CrossOrganizationException("Item {$item->id} does not belong to Organization {$booking->organization_id}. Found {$itemOrgId}.");
        }
        if ((int) $itemBu->id !== (int) $booking->business_unit_id) {
            throw new CrossOrganizationException("Item {$item->id} does not belong to Business Unit {$booking->business_unit_id}. Found {$itemBu->id}.");
        }

        $priceStr = bcadd((string) ($unitPrice ?? $item->base_price ?? '0'), '0', 4);
        if (bccomp($priceStr, '0', 4) < 0) {
            throw new InvalidArgumentException('Package unit price cannot be negative.');
        }

        $taxRateStr = '0.00';
        if ($taxCategory) {
            if ((int) $taxCategory->organization_id !== (int) $booking->organization_id) {
                throw new CrossOrganizationException("Tax Category {$taxCategory->id} does not belong to Organization {$booking->organization_id}.");
            }
            if ($taxCategory->business_unit_id && (int) $taxCategory->business_unit_id !== (int) $booking->business_unit_id) {
                throw new CrossOrganizationException("Tax Category {$taxCategory->id} does not belong to Business Unit {$booking->business_unit_id}.");
            }
            $taxRateStr = bcadd((string) ($taxCategory->rate ?? '0'), '0', 2);
        }

        $subtotal = bcmul($priceStr, $qtyStr, 4);
        $taxAmount = bcmul($subtotal, bcdiv($taxRateStr, '100', 6), 4);
        $total = bcadd($subtotal, $taxAmount, 4);

        return DB::transaction(function () use ($booking, $item, $taxCategory, $qtyStr, $priceStr, $subtotal, $taxAmount, $total, $notes) {
            $package = EventBookingPackage::create([
                'event_booking_id' => $booking->id,
                'item_id' => $item->id,
                'unit_id' => $item->unit_id,
                'quantity' => $qtyStr,
                'unit_price' => $priceStr,
                'subtotal' => $subtotal,
                'tax_category_id' => $taxCategory?->id,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'notes' => $notes,
            ]);

            $this->recalculateBookingTotals($booking);

            return $package;
        });
    }

    public function addAttendee(
        EventBooking $booking,
        string $name,
        ?string $email = null,
        ?string $phone = null,
        bool $vipStatus = false,
        ?string $notes = null
    ): EventBookingAttendee {
        if (in_array($booking->status, [EventBookingStatus::COMPLETED, EventBookingStatus::CANCELLED])) {
            throw new InvalidStateTransitionException("Cannot add attendees to booking {$booking->booking_number} in terminal status {$booking->status->value}.");
        }

        return EventBookingAttendee::create([
            'event_booking_id' => $booking->id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'vip_status' => $vipStatus,
            'notes' => $notes,
        ]);
    }

    public function quoteBooking(EventBooking $booking, ?User $user = null): EventBooking
    {
        if ($booking->status !== EventBookingStatus::DRAFT) {
            throw new InvalidStateTransitionException("Cannot quote booking {$booking->booking_number} from status {$booking->status->value}. Must be in DRAFT.");
        }

        $booking->status = EventBookingStatus::QUOTED;
        $booking->save();

        return $booking;
    }

    public function confirmBooking(EventBooking $booking, ?User $user = null): EventBooking
    {
        if (! in_array($booking->status, [EventBookingStatus::DRAFT, EventBookingStatus::QUOTED])) {
            throw new InvalidStateTransitionException("Cannot confirm booking {$booking->booking_number} from status {$booking->status->value}.");
        }

        if (bccomp((string) $booking->deposit_required_amount, (string) $booking->total_amount, 4) > 0) {
            throw new InvalidArgumentException("Deposit required amount ({$booking->deposit_required_amount}) cannot exceed total booking amount ({$booking->total_amount}).");
        }

        return DB::transaction(function () use ($booking, $user) {
            $lockedBooking = EventBooking::where('id', $booking->id)->lockForUpdate()->first();

            if (! in_array($lockedBooking->status, [EventBookingStatus::DRAFT, EventBookingStatus::QUOTED])) {
                throw new InvalidStateTransitionException("Booking {$lockedBooking->booking_number} is already in status {$lockedBooking->status->value}.");
            }

            // Generate deposit invoice idempotently if required
            if (bccomp((string) $lockedBooking->deposit_required_amount, '0', 4) > 0 && ! $lockedBooking->deposit_invoice_id) {
                ProcessedEvent::process('EventBookingDepositInvoice', (string) $lockedBooking->id, function () use ($lockedBooking, $user) {
                    $packageItem = $lockedBooking->packages->first()?->item;
                    if (! $packageItem) {
                        // Fallback to any item or require package
                        $packageItem = Item::withoutGlobalScopes()->where('organization_id', $lockedBooking->organization_id)->first();
                    }

                    if (! $packageItem) {
                        throw new InvalidArgumentException('Cannot generate deposit invoice without at least one item item in organization.');
                    }

                    $invoice = $this->invoiceService->createDraft(
                        branch: $lockedBooking->branch,
                        customer: $lockedBooking->customer,
                        invoiceNumber: null,
                        order: null,
                        currency: 'TZS',
                        userId: $user?->id
                    );

                    $this->invoiceService->addLine(
                        invoice: $invoice,
                        item: $packageItem,
                        quantity: 1,
                        unitPrice: $lockedBooking->deposit_required_amount,
                        taxCategory: null,
                        description: "Deposit for Event Booking #{$lockedBooking->booking_number}"
                    );

                    $this->invoiceService->issueInvoice($invoice);

                    $lockedBooking->deposit_invoice_id = $invoice->id;
                });
            }

            $lockedBooking->status = EventBookingStatus::CONFIRMED;
            $lockedBooking->confirmed_at = now();
            $lockedBooking->confirmed_by = $user?->id;
            $lockedBooking->save();

            return $lockedBooking;
        });
    }

    public function startBooking(EventBooking $booking, ?User $user = null): EventBooking
    {
        if ($booking->status !== EventBookingStatus::CONFIRMED) {
            throw new InvalidStateTransitionException("Cannot start booking {$booking->booking_number} from status {$booking->status->value}. Must be CONFIRMED.");
        }

        $booking->status = EventBookingStatus::IN_PROGRESS;
        $booking->save();

        return $booking;
    }

    public function completeBooking(EventBooking $booking, ?User $user = null): EventBooking
    {
        if ($booking->status !== EventBookingStatus::IN_PROGRESS) {
            throw new InvalidStateTransitionException("Cannot complete booking {$booking->booking_number} from status {$booking->status->value}. Must be IN_PROGRESS.");
        }

        return DB::transaction(function () use ($booking, $user) {
            $lockedBooking = EventBooking::where('id', $booking->id)->lockForUpdate()->first();

            if ($lockedBooking->status !== EventBookingStatus::IN_PROGRESS) {
                throw new InvalidStateTransitionException("Booking {$lockedBooking->booking_number} is in status {$lockedBooking->status->value}, expected IN_PROGRESS.");
            }

            // Calculate net balance due
            $balance = bcsub((string) $lockedBooking->total_amount, (string) $lockedBooking->deposit_paid_amount, 4);

            if (bccomp($balance, '0', 4) > 0 && ! $lockedBooking->final_invoice_id) {
                ProcessedEvent::process('EventBookingFinalInvoice', (string) $lockedBooking->id, function () use ($lockedBooking, $user) {
                    $invoice = $this->invoiceService->createDraft(
                        branch: $lockedBooking->branch,
                        customer: $lockedBooking->customer,
                        invoiceNumber: null,
                        order: null,
                        currency: 'TZS',
                        userId: $user?->id
                    );

                    foreach ($lockedBooking->packages as $pkg) {
                        $this->invoiceService->addLine(
                            invoice: $invoice,
                            item: $pkg->item,
                            quantity: $pkg->quantity,
                            unitPrice: $pkg->unit_price,
                            taxCategory: $pkg->taxCategory,
                            description: $pkg->notes ?? "Package: {$pkg->item->name}"
                        );
                    }

                    if (bccomp((string) $lockedBooking->deposit_paid_amount, '0', 4) > 0) {
                        $depositItem = Item::withoutGlobalScopes()->where('business_unit_id', $lockedBooking->business_unit_id)->first();

                        if (! $depositItem) {
                            throw new InvalidArgumentException('Cannot apply deposit: no valid item found in business unit.');
                        }

                        $this->invoiceService->addLine(
                            invoice: $invoice,
                            item: $depositItem,
                            quantity: 1,
                            unitPrice: '-'.(string) $lockedBooking->deposit_paid_amount,
                            taxCategory: null, // Deposit is generally tax-inclusive or already taxed on the deposit invoice
                            description: "Deposit Applied from Invoice #{$lockedBooking->deposit_invoice_id}"
                        );
                    }

                    $this->invoiceService->issueInvoice($invoice);
                    $lockedBooking->final_invoice_id = $invoice->id;
                });
            }

            $lockedBooking->status = EventBookingStatus::COMPLETED;
            $lockedBooking->completed_at = now();
            $lockedBooking->completed_by = $user?->id;
            $lockedBooking->save();

            return $lockedBooking;
        });
    }

    public function cancelBooking(EventBooking $booking, ?User $user = null): EventBooking
    {
        if (in_array($booking->status, [EventBookingStatus::COMPLETED, EventBookingStatus::CANCELLED])) {
            throw new InvalidStateTransitionException("Cannot cancel booking {$booking->booking_number} in terminal status {$booking->status->value}.");
        }

        $booking->status = EventBookingStatus::CANCELLED;
        $booking->save();

        return $booking;
    }

    public function recalculateBookingTotals(EventBooking $booking): void
    {
        $packages = EventBookingPackage::where('event_booking_id', $booking->id)->get();

        $subtotal = '0.0000';
        $taxAmount = '0.0000';
        $totalAmount = '0.0000';

        foreach ($packages as $pkg) {
            $subtotal = bcadd($subtotal, (string) $pkg->subtotal, 4);
            $taxAmount = bcadd($taxAmount, (string) $pkg->tax_amount, 4);
            $totalAmount = bcadd($totalAmount, (string) $pkg->total, 4);
        }

        $booking->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
        $booking->save();
    }
}
