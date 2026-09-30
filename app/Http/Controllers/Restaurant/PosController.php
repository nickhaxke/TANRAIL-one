<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ItemCategory;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\OrderLine;
use App\Domains\Core\Models\Payment;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Core\Services\InventoryService;
use App\Domains\Modules\Restaurant\Models\RestaurantShift;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Resolve active station/branch for the user
        $branch = $this->resolveActiveBranch($user, $request);

        // Fetch catering business unit or branch business unit
        $cateringBu = $branch->businessUnit ?? BusinessUnit::where('category', 'like', '%Catering%')
            ->orWhere('code', 'like', '%CAT%')
            ->first();

        if ($cateringBu) {
            app(ContextManager::class)->setActiveBusinessUnit($cateringBu);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        $stockBalances = [];
        if ($location) {
            $stockBalances = StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->pluck('quantity', 'item_id')
                ->toArray();
        }

        $itemsQuery = Item::withoutGlobalScopes()->with('category')->where('status', true)->where('can_be_sold', true);
        if ($cateringBu) {
            $itemsQuery->where('business_unit_id', $cateringBu->id);
        }
        
        $items = $itemsQuery->get()->map(function ($item) use ($stockBalances) {
            $inStock = $item->track_inventory ? (float) ($stockBalances[$item->id] ?? 0) : 9999;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'category' => $item->category?->name ?? 'Uncategorized',
                'category_id' => $item->category_id,
                'price' => (float) $item->base_price,
                'cost' => (float) $item->standard_cost,
                'track_inventory' => (bool) $item->track_inventory,
                'stock' => $inStock,
                'description' => $item->description,
            ];
        });

        // Load real categories from the database
        $businessUnit = $cateringBu ?? $branch->businessUnit ?? BusinessUnit::first();
        $categories = ItemCategory::where('business_unit_id', $businessUnit->id)->orderBy('name')->get();

        $allBranches = Branch::where('status', true)->get();

        $activeShift = RestaurantShift::where('branch_id', $branch->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        return view('restaurant.pos.index', compact('branch', 'items', 'user', 'allBranches', 'activeShift', 'categories'));
    }

    public function storeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'dining_type' => 'required|in:dine_in,takeaway,train_delivery',
            'table_number' => 'nullable|string|max:50',
            'customer_notes' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,mobile_money,card',
            'amount_paid' => 'required|numeric|min:0',
            'payment_reference' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $branch = Branch::with(['businessUnit'])->findOrFail($validated['branch_id']);

        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);

        // Inventory Stock Pre-flight Validation
        if ($location) {
            foreach ($validated['items'] as $itemData) {
                $item = Item::withoutGlobalScopes()->findOrFail($itemData['id']);
                if ($item->track_inventory) {
                    $balance = StockBalance::withoutGlobalScopes()
                        ->where('inventory_location_id', $location->id)
                        ->where('item_id', $item->id)
                        ->first();
                    $available = $balance ? (float) ($balance->quantity - $balance->reserved_quantity) : 0;
                    if ($available < (float) $itemData['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$item->name}. Available in station store: {$available}, Requested: {$itemData['quantity']}.",
                        ], 422);
                    }
                }
            }
        }

        return DB::transaction(function () use ($validated, $branch, $location) {
            $activeShift = RestaurantShift::where('branch_id', $branch->id)
                ->where('user_id', Auth::id())
                ->where('status', 'open')
                ->latest()
                ->first();

            $order = new Order;
            $order->branch_id = $branch->id;
            $order->business_unit_id = $branch->business_unit_id;
            $order->status = OrderStatus::CONFIRMED;
            $order->created_by = Auth::id();
            $order->shift_id = $activeShift?->id;
            $order->subtotal = 0;
            $order->tax_total = 0;
            $order->total = 0;
            $order->save();

            $subtotal = 0;
            $taxTotal = 0;
            $total = 0;

            foreach ($validated['items'] as $itemData) {
                $item = Item::withoutGlobalScopes()->findOrFail($itemData['id']);
                $qty = (float) $itemData['quantity'];
                $lineSubtotal = (float) $item->base_price * $qty;
                $lineTax = round($lineSubtotal * 0.18, 2); // Standard 18% TRA VAT
                $lineTotal = $lineSubtotal + $lineTax;

                OrderLine::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->base_price,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
                $total += $lineTotal;
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'total' => $total,
            ]);

            if ($activeShift) {
                $activeShift->increment('total_sales', $total);
                $activeShift->increment('total_orders_count', 1);
            }

            // Deduct inventory through InventoryService
            $updatedStock = [];
            if ($location) {
                $inventoryService = app(InventoryService::class);
                foreach ($validated['items'] as $itemData) {
                    $item = Item::withoutGlobalScopes()->findOrFail($itemData['id']);
                    $qty = (float) $itemData['quantity'];
                    if ($item->track_inventory) {
                        $balance = StockBalance::withoutGlobalScopes()
                            ->where('inventory_location_id', $location->id)
                            ->where('item_id', $item->id)
                            ->first();
                        if ($balance) {
                            $inventoryService->issue(
                                $item,
                                $location,
                                $qty,
                                'order',
                                $order->id,
                                Auth::id()
                            );
                            $updatedStock[$item->id] = (float) $balance->fresh()->quantity;
                        }
                    }
                }
            }

            $paymentMethod = $validated['payment_method'];
            $paymentRef = $validated['payment_reference'] ?? ('TXN-'.strtoupper(Str::random(8)));

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $paymentMethod,
                'reference' => $paymentRef,
                'created_by' => Auth::id(),
            ]);

            $order->load(['lines.item' => fn ($q) => $q->withoutGlobalScopes(), 'branch']);

            return response()->json([
                'success' => true,
                'message' => 'Order completed, stock deducted, and ticket sent to kitchen!',
                'updated_stock' => $updatedStock,
                'receipt' => [
                    'order_id' => $order->id,
                    'receipt_number' => 'TANRAIL-POS-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'timestamp' => $order->created_at->format('d M Y, H:i:s'),
                    'station_name' => $branch->name,
                    'station_city' => $branch->city ?? 'Dar es Salaam',
                    'cashier_name' => Auth::user()->name,
                    'dining_type' => ucwords(str_replace('_', ' ', $validated['dining_type'])),
                    'table_number' => $validated['table_number'] ?? 'Counter Service',
                    'subtotal' => number_format($subtotal, 2),
                    'tax_total' => number_format($taxTotal, 2),
                    'total' => number_format($total, 2),
                    'total_raw' => $total,
                    'amount_paid' => number_format((float) $validated['amount_paid'], 2),
                    'change' => number_format(max(0, (float) $validated['amount_paid'] - $total), 2),
                    'payment_method' => strtoupper(str_replace('_', ' ', $paymentMethod)),
                    'payment_ref' => $paymentRef,
                    'items' => $order->lines->map(fn ($line) => [
                        'name' => $line->item->name,
                        'qty' => (int) $line->quantity,
                        'price' => number_format((float) $line->unit_price, 2),
                        'total' => number_format((float) $line->total, 2),
                    ]),
                ],
            ]);
        });
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        $orders = Order::withoutGlobalScopes()
            ->with(['lines.item' => fn ($q) => $q->withoutGlobalScopes(), 'payments', 'createdBy', 'branch'])
            ->where('branch_id', $branch->id)
            ->latest()
            ->paginate(20);

        return view('restaurant.orders.index', compact('branch', 'orders', 'user'));
    }

    private function resolveActiveBranch($user, Request $request): Branch
    {
        if ($request->has('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                return $branch;
            }
        }

        // Try user scoped branch in role_user
        $scopedBranchId = $user->roles()
            ->wherePivot('scope_type', Branch::class)
            ->value('scope_id');

        if ($scopedBranchId) {
            $branch = Branch::find($scopedBranchId);
            if ($branch) {
                return $branch;
            }
        }

        // Default to a restaurant branch or the first active branch
        return Branch::where('facility_type', 'like', '%Restaurant%')
            ->orWhere('name', 'like', '%Restaurant%')
            ->first() ?? Branch::first() ?? Branch::create([
                'name' => 'SGR Dar es Salaam Main Concourse Restaurant',
                'code' => 'BR-DAR-REST',
                'city' => 'Dar es Salaam',
                'status' => true,
            ]);
    }
}
