<?php

namespace App\Providers;

use App\Domains\Core\Events\CreditNoteIssued;
use App\Domains\Core\Events\InvoiceIssued;
use App\Domains\Core\Events\OrderCancelled;
use App\Domains\Core\Events\OrderConfirmed;
use App\Domains\Core\Events\PaymentAllocated;
use App\Domains\Core\Events\PurchaseReceiptConfirmed;
use App\Domains\Core\Events\SalesPaymentReceived;
use App\Domains\Core\Events\SupplierInvoiceApproved;
use App\Domains\Core\Events\SupplierPaymentAllocated;
use App\Domains\Core\Events\SupplierPaymentRecorded;
use App\Domains\Core\Listeners\DeductInventoryListener;
use App\Domains\Core\Listeners\PostCreditNoteToGlListener;
use App\Domains\Core\Listeners\PostCustomerPaymentToGlListener;
use App\Domains\Core\Listeners\PostInvoiceToGlListener;
use App\Domains\Core\Listeners\PostSupplierInvoiceToGlListener;
use App\Domains\Core\Listeners\PostSupplierPaymentToGlListener;
use App\Domains\Core\Listeners\ReceiveInventoryListener;
use App\Domains\Core\Listeners\RecordSalesPaymentListener;
use App\Domains\Core\Listeners\RecordSupplierPaymentListener;
use App\Domains\Core\Listeners\RestoreInventoryListener;
use App\Domains\Core\Models\Account;
use App\Domains\Core\Models\CreditNote;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Journal;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\PaymentAllocation;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseReceipt;
use App\Domains\Core\Models\StockMovement;
use App\Domains\Core\Models\StockTransfer;
use App\Domains\Core\Models\Supplier;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Policies\AccountPolicy;
use App\Domains\Core\Policies\CreditNotePolicy;
use App\Domains\Core\Policies\CustomerPolicy;
use App\Domains\Core\Policies\InventoryLocationPolicy;
use App\Domains\Core\Policies\InvoicePolicy;
use App\Domains\Core\Policies\ItemPolicy;
use App\Domains\Core\Policies\JournalPolicy;
use App\Domains\Core\Policies\OrderPolicy;
use App\Domains\Core\Policies\PaymentAllocationPolicy;
use App\Domains\Core\Policies\PurchaseOrderPolicy;
use App\Domains\Core\Policies\PurchaseReceiptPolicy;
use App\Domains\Core\Policies\StockMovementPolicy;
use App\Domains\Core\Policies\StockTransferPolicy;
use App\Domains\Core\Policies\SupplierInvoicePolicy;
use App\Domains\Core\Policies\SupplierPolicy;
use App\Domains\Modules\EventManagement\Models\EventBooking;
use App\Domains\Modules\EventManagement\Policies\EventBookingPolicy;
use App\Domains\Modules\Production\Events\ProductionCompleted;
use App\Domains\Modules\Production\Listeners\PostProductionCompletedToGlListener;
use App\Domains\Modules\Production\Models\BillOfMaterials;
use App\Domains\Modules\Production\Models\ProductionOrder;
use App\Domains\Modules\Production\Policies\BillOfMaterialsPolicy;
use App\Domains\Modules\Production\Policies\ProductionOrderPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);

        // Phase 3.2 Inventory
        Gate::policy(InventoryLocation::class, InventoryLocationPolicy::class);
        Gate::policy(StockMovement::class, StockMovementPolicy::class);
        Gate::policy(StockTransfer::class, StockTransferPolicy::class);

        // Phase 3.2.3 Sales
        Gate::policy(Order::class, OrderPolicy::class);
        Event::listen(OrderConfirmed::class, DeductInventoryListener::class);
        Event::listen(OrderCancelled::class, RestoreInventoryListener::class);

        // Phase 3.2.4 Procurement
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(PurchaseReceipt::class, PurchaseReceiptPolicy::class);
        Event::listen(PurchaseReceiptConfirmed::class, ReceiveInventoryListener::class);

        // Phase 3.3 Financial Core
        Event::listen(SalesPaymentReceived::class, RecordSalesPaymentListener::class);
        Event::listen(SupplierPaymentRecorded::class, RecordSupplierPaymentListener::class);
        Gate::policy(Journal::class, JournalPolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);

        // Phase 4.7 Financial Ledger Integration & GL Automations
        Event::listen(InvoiceIssued::class, PostInvoiceToGlListener::class);
        Event::listen(PaymentAllocated::class, PostCustomerPaymentToGlListener::class);
        Event::listen(SupplierInvoiceApproved::class, PostSupplierInvoiceToGlListener::class);
        Event::listen(SupplierPaymentAllocated::class, PostSupplierPaymentToGlListener::class);
        Event::listen(CreditNoteIssued::class, PostCreditNoteToGlListener::class);

        // Stage 4.8 Commercial Sub-Ledger Policies
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(SupplierInvoice::class, SupplierInvoicePolicy::class);
        Gate::policy(CreditNote::class, CreditNotePolicy::class);
        Gate::policy(PaymentAllocation::class, PaymentAllocationPolicy::class);

        // Phase 5.1 & 5.2 Reusable Capabilities Policies
        Gate::policy(BillOfMaterials::class, BillOfMaterialsPolicy::class);
        Gate::policy(ProductionOrder::class, ProductionOrderPolicy::class);
        Gate::policy(EventBooking::class, EventBookingPolicy::class);

        // Phase 5.4 GL Integration
        Event::listen(ProductionCompleted::class, PostProductionCompletedToGlListener::class);
    }
}
