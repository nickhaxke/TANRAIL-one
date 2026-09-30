<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Supplier;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseOrderLine;
use App\Domains\Core\Enums\PurchaseOrderStatus;
use App\Domains\Core\Services\InventoryService;
use App\Domains\Core\Models\InventoryLocation;
use Illuminate\Support\Facades\Auth;

class RestaurantPurchasingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Show only submitted/pending requests here
        $requests = PurchaseOrder::with(['supplier', 'lines.item'])
            ->whereIn('status', [PurchaseOrderStatus::DRAFT, PurchaseOrderStatus::SUBMITTED])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $branch = \App\Domains\Core\Models\Branch::find($user->branch_id) ?? \App\Domains\Core\Models\Branch::first();
        $locations = InventoryLocation::where('branch_id', $branch->id)->where('status', 'active')->get();
            
        return view('restaurant.purchasing.requests', compact('requests', 'locations'));
    }

    public function orders()
    {
        // Show Approved, PartiallyReceived, and Completed orders here
        $orders = PurchaseOrder::with(['supplier', 'lines.item'])
            ->whereIn('status', [PurchaseOrderStatus::APPROVED, PurchaseOrderStatus::PARTIAL_RECEIVED, PurchaseOrderStatus::RECEIVED])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $user = Auth::user();
        $branch = \App\Domains\Core\Models\Branch::find($user->branch_id) ?? \App\Domains\Core\Models\Branch::first();
        $locations = InventoryLocation::where('branch_id', $branch->id)->where('status', 'active')->get();
            
        return view('restaurant.purchasing.orders', compact('orders', 'locations'));
    }

    public function suppliers()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('restaurant.purchasing.suppliers', compact('suppliers'));
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
        ]);

        $bu = \App\Domains\Core\Models\BusinessUnit::where('type', 'restaurant')->first() ?? \App\Domains\Core\Models\BusinessUnit::first();
        $orgId = $bu ? $bu->organization_id : 1;

        $contactDetails = json_encode([
            'contact_person' => $request->input('contact_person'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'bank_name' => $request->input('bank_name'),
            'account_number' => $request->input('account_number'),
        ]);

        Supplier::create([
            'organization_id' => $orgId,
            'name' => $request->input('name'),
            'tax_number' => $request->input('tax_number'),
            'contact_details' => $contactDetails,
            'status' => true
        ]);

        return back()->with('success', 'Supplier added successfully!');
    }

    private function resolveActiveBranch($user, Request $request): \App\Domains\Core\Models\Branch
    {
        if ($request->has('branch_id')) {
            $branch = \App\Domains\Core\Models\Branch::find($request->input('branch_id'));
            if ($branch) {
                return $branch;
            }
        }

        $scopedBranchId = $user->roles()
            ->wherePivot('scope_type', \App\Domains\Core\Models\Branch::class)
            ->value('scope_id');

        if ($scopedBranchId) {
            $branch = \App\Domains\Core\Models\Branch::find($scopedBranchId);
            if ($branch) {
                return $branch;
            }
        }

        return \App\Domains\Core\Models\Branch::where('facility_type', 'like', '%Restaurant%')
            ->orWhere('name', 'like', '%Restaurant%')
            ->first() ?? \App\Domains\Core\Models\Branch::first();
    }

    public function createRequest(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);
        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();
        
        $suppliers = Supplier::where('status', true)->get();
        $categories = \App\Domains\Core\Models\ItemCategory::where('business_unit_id', $businessUnit->id)->get();
        $units = \App\Domains\Core\Models\Unit::all();
        
        return view('restaurant.purchasing.create-request', compact('businessUnit', 'suppliers', 'categories', 'units'));
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'business_unit_id' => 'required|exists:business_units,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.category_id' => 'required|exists:item_categories,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $bu = BusinessUnit::find($request->input('business_unit_id'));
        $total = 0;
        foreach ($request->input('items') as $lineItem) {
            $total += ($lineItem['quantity'] * $lineItem['unit_price']);
        }

        $po = new PurchaseOrder();
        $po->business_unit_id = $bu->id;
        $po->branch_id = $bu->branches()->first()->id ?? null;
        $po->supplier_id = $request->input('supplier_id');
        $po->status = $request->input('action') === 'draft' ? PurchaseOrderStatus::DRAFT : PurchaseOrderStatus::SUBMITTED;
        $po->subtotal = $total;
        $po->tax_total = 0;
        $po->total = $total;
        $po->created_by = auth()->id();
        $po->save();
        
        $po->reference_number = 'REQ-REST-' . str_pad($po->id, 4, '0', STR_PAD_LEFT);
        $po->save();

        foreach ($request->input('items') as $lineItem) {
            // Check if item exists by name in this BU, if not create it
            $item = \App\Domains\Core\Models\Item::firstOrCreate([
                'name' => $lineItem['name'],
                'business_unit_id' => $bu->id
            ], [
                'sku' => 'REQ-'.strtoupper(\Illuminate\Support\Str::random(6)),
                'type' => 'physical',
                'category_id' => $lineItem['category_id'],
                'unit_id' => $lineItem['unit_id'],
                'track_inventory' => true,
                'can_be_sold' => false,
                'can_be_purchased' => true,
                'base_price' => $lineItem['unit_price'] * 1.5,
                'standard_cost' => $lineItem['unit_price'],
                'status' => true
            ]);

            $poLine = new PurchaseOrderLine();
            $poLine->purchase_order_id = $po->id;
            $poLine->item_id = $item->id;
            $poLine->quantity = $lineItem['quantity'];
            $poLine->unit_price = $lineItem['unit_price'];
            $poLine->subtotal = $lineItem['quantity'] * $lineItem['unit_price'];
            $poLine->tax_amount = 0;
            $poLine->total = $poLine->subtotal;
            $poLine->received_quantity = 0;
            $poLine->save();
        }

        $msg = $request->input('action') === 'draft' ? 'Purchase Request saved as draft.' : 'Purchase Request submitted successfully.';
        return redirect()->route('restaurant.purchasing.requests')->with('success', $msg);
    }

    public function editRequest($id)
    {
        $purchaseRequest = PurchaseOrder::with(['lines.item', 'supplier'])->findOrFail($id);
        
        // Ensure it's a draft
        if ($purchaseRequest->status !== PurchaseOrderStatus::DRAFT) {
            return redirect()->route('restaurant.purchasing.requests')->with('error', 'Only draft requests can be edited.');
        }

        $businessUnit = \App\Domains\Core\Models\BusinessUnit::find(app(\App\Domains\Core\Services\ContextManager::class)->getActiveBusinessUnitId());
        if (!$businessUnit) {
            $businessUnit = BusinessUnit::first(); // Fallback
        }

        $suppliers = Supplier::where('status', true)->get();
        $categories = \App\Domains\Core\Models\ItemCategory::where('business_unit_id', $businessUnit->id)
                        ->where('status', 1)
                        ->get();
        $units = \App\Domains\Core\Models\Unit::all();

        return view('restaurant.purchasing.edit-request', compact('businessUnit', 'suppliers', 'categories', 'units', 'purchaseRequest'));
    }

    public function updateRequest(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status !== PurchaseOrderStatus::DRAFT) {
            return redirect()->route('restaurant.purchasing.requests')->with('error', 'Only draft requests can be edited.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
        ]);

        $po->supplier_id = $request->input('supplier_id');
        $po->status = $request->input('action') === 'draft' ? PurchaseOrderStatus::DRAFT : PurchaseOrderStatus::SUBMITTED;
        
        $total = 0;
        foreach ($request->input('items') as $lineItem) {
            $total += ($lineItem['quantity'] * $lineItem['unit_price']);
        }
        
        $po->subtotal = $total;
        $po->total = $total;
        $po->save();

        // Delete old lines
        PurchaseOrderLine::where('purchase_order_id', $po->id)->delete();

        // Recreate lines
        $bu = \App\Domains\Core\Models\BusinessUnit::find(app(\App\Domains\Core\Services\ContextManager::class)->getActiveBusinessUnitId()) ?? BusinessUnit::first();
        foreach ($request->input('items') as $lineItem) {
            $item = \App\Domains\Core\Models\Item::firstOrCreate([
                'name' => $lineItem['name'],
                'business_unit_id' => $bu->id
            ], [
                'sku' => 'REQ-'.strtoupper(\Illuminate\Support\Str::random(6)),
                'type' => 'physical',
                'category_id' => $lineItem['category_id'],
                'unit_id' => $lineItem['unit_id'],
                'track_inventory' => true,
                'base_price' => $lineItem['unit_price'] * 1.5,
                'standard_cost' => $lineItem['unit_price'],
                'status' => true
            ]);

            $poLine = new PurchaseOrderLine();
            $poLine->purchase_order_id = $po->id;
            $poLine->item_id = $item->id;
            $poLine->quantity = $lineItem['quantity'];
            $poLine->unit_price = $lineItem['unit_price'];
            $poLine->subtotal = $lineItem['quantity'] * $lineItem['unit_price'];
            $poLine->tax_amount = 0;
            $poLine->total = $poLine->subtotal;
            $poLine->received_quantity = 0;
            $poLine->save();
        }

        $msg = $request->input('action') === 'draft' ? 'Draft Purchase Request updated.' : 'Purchase Request submitted successfully.';
        return redirect()->route('restaurant.purchasing.requests')->with('success', $msg);
    }

    public function submitDraft(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status !== PurchaseOrderStatus::DRAFT) {
            return back()->with('error', 'Only draft requests can be submitted.');
        }
        $po->status = PurchaseOrderStatus::SUBMITTED;
        $po->save();
        return back()->with('success', 'Purchase Request submitted successfully.');
    }

    public function receive(Request $request, $id)
    {
        $po = PurchaseOrder::with('lines.item')->findOrFail($id);
        
        // Ensure only approved or partially received POs can be received
        if (!in_array($po->status->value, ['approved', 'partial_received'])) {
            return back()->with('error', 'This purchase order cannot be received at this time.');
        }

        $receivedQtys = $request->input('received_qty', []);
        
        $inventoryService = app(InventoryService::class);
        $branch = $po->branch;
        
        $locationId = $request->input('location_id');
        if (!$locationId) {
            return back()->with('error', 'Please select a destination store location to receive goods.');
        }

        $location = InventoryLocation::withoutGlobalScopes()
                                    ->where('branch_id', $branch->id)
                                    ->where('status', 'active')
                                    ->where('id', $locationId)
                                    ->first();

        if (!$location) {
            return back()->with('error', 'Selected location is invalid or not active for this branch.');
        }

        $receivedLines = $request->input('receive_line', []);
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($po, $receivedQtys, $receivedLines, $inventoryService, $location) {
                $allReceived = true;
                foreach ($po->lines as $line) {
                    $qtyToReceive = isset($receivedQtys[$line->id]) ? (float) $receivedQtys[$line->id] : 0;
                    
                    // Only process if the user marked the checkbox for this line
                    if (isset($receivedLines[$line->id]) && $qtyToReceive > 0) {
                        // Update PO line received quantity
                        $line->received_quantity += $qtyToReceive;
                        $line->save();
                        
                        // Add to inventory store via InventoryService
                        $inventoryService->receive(
                            $line->item,
                            $location,
                            $qtyToReceive,
                            'receive', // type of movement
                            $po->id, // reference id
                            Auth::id()
                        );
                    }
                    
                    // Check if this line is fully received
                    if ($line->received_quantity < $line->quantity) {
                        $allReceived = false;
                    }
                }
                
                // Update PO status
                if ($allReceived) {
                    $po->status = PurchaseOrderStatus::RECEIVED;
                } else {
                    $po->status = PurchaseOrderStatus::PARTIAL_RECEIVED;
                }
                $po->save();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error while receiving: ' . $e->getMessage());
        }

        return back()->with('success', 'Goods received successfully and added to your Store Inventory!');
    }
}
