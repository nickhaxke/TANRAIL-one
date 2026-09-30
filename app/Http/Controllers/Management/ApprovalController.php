<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Services\PurchaseService;
use App\Domains\Core\Services\SupplierInvoiceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Core\Enums\PurchaseOrderStatus;
use App\Domains\Core\Enums\SupplierInvoiceStatus;

class ApprovalController extends Controller
{
    public function index()
    {
        $businessUnit = \App\Domains\Core\Models\BusinessUnit::find(app(\App\Domains\Core\Services\ContextManager::class)->getActiveBusinessUnitId());
        
        $poQuery = PurchaseOrder::with(['supplier', 'businessUnit', 'lines.item'])
            ->where('status', 'submitted');
            
        $invoiceQuery = SupplierInvoice::with(['supplier', 'businessUnit'])
            ->where('status', 'draft');

        if ($businessUnit) {
            $poQuery->where('business_unit_id', $businessUnit->id);
            $invoiceQuery->where('business_unit_id', $businessUnit->id);
        }

        $purchaseOrders = $poQuery->orderBy('created_at', 'desc')->get();
        $supplierInvoices = $invoiceQuery->orderBy('created_at', 'desc')->get();

        return view('management.approvals.index', compact('purchaseOrders', 'supplierInvoices'));
    }

    public function approvePurchaseOrder(Request $request, PurchaseOrder $order, PurchaseService $purchaseService, SupplierInvoiceService $invoiceService)
    {
        try {
            $purchaseService->approveOrder($order);

            // Create Supplier Invoice (Debt) directly upon approval
            $bill = $invoiceService->createDraft(
                $order->branch,
                $order->supplier,
                'BILL-' . $order->reference_number,
                'REF-' . $order->id,
                $order,
                auth()->id() ?? 3 // fallback to demo user
            );

            // Add lines to the bill
            foreach ($order->lines as $line) {
                $invoiceService->addLine(
                    $bill,
                    $line->item,
                    $line->quantity,
                    $line->unit_price
                );
            }

            // Immediately mark it as approved/ready to pay if they want it straight to debt
            $bill->status = SupplierInvoiceStatus::APPROVED;
            $bill->approved_by = auth()->id() ?? 3;
            $bill->approved_at = now();
            $bill->balance_due = $bill->total;
            $bill->save();

            return back()->with('success', "Purchase Order {$order->reference_number} approved successfully and Bill generated.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectPurchaseOrder(Request $request, PurchaseOrder $order)
    {
        try {
            // Revert back to draft so Restaurant can see/edit it
            $order->status = PurchaseOrderStatus::DRAFT;
            $order->save();
            return back()->with('success', "Purchase Order {$order->reference_number} rejected and returned to draft.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}