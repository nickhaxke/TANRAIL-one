<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        // Fetch active kitchen orders
        $activeOrders = Order::withoutGlobalScopes()
            ->with(['lines.item' => fn ($q) => $q->withoutGlobalScopes(), 'createdBy'])
            ->where('branch_id', $branch->id)
            ->whereIn('status', [
                OrderStatus::CONFIRMED->value,
                OrderStatus::PREPARING->value,
                OrderStatus::READY->value,
            ])
            ->oldest()
            ->get()
            ->map(function ($order) {
                $elapsedMins = (int) $order->created_at->diffInMinutes(now());

                return [
                    'id' => $order->id,
                    'order_number' => 'ORD-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'status' => $order->status->value,
                    'created_at' => $order->created_at->format('H:i'),
                    'elapsed_minutes' => $elapsedMins,
                    'cashier' => $order->createdBy?->name ?? 'POS Station',
                    'items' => $order->lines->map(fn ($l) => [
                        'name' => $l->item->name,
                        'quantity' => (int) $l->quantity,
                    ]),
                ];
            });

        $completedCountToday = Order::withoutGlobalScopes()
            ->where('branch_id', $branch->id)
            ->where('created_at', 'like', now()->format('Y-m-d').'%')
            ->whereIn('status', [OrderStatus::FULFILLED->value, OrderStatus::COMPLETED->value])
            ->count();

        return view('restaurant.kitchen.index', compact('branch', 'activeOrders', 'completedCountToday', 'user'));
    }

    public function updateStatus(Request $request, int $orderId): JsonResponse
    {
        $order = Order::withoutGlobalScopes()->findOrFail($orderId);

        $validated = $request->validate([
            'status' => 'required|in:preparing,ready,completed,fulfilled',
        ]);

        $newStatus = match ($validated['status']) {
            'preparing' => OrderStatus::PREPARING,
            'ready' => OrderStatus::READY,
            'completed', 'fulfilled' => OrderStatus::COMPLETED,
        };

        $order->status = $newStatus;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Kitchen status updated to '.strtoupper($validated['status']),
            'new_status' => $newStatus->value,
        ]);
    }

    private function resolveActiveBranch($user, Request $request): Branch
    {
        if ($request->has('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                return $branch;
            }
        }

        $scopedBranchId = $user->roles()
            ->wherePivot('scope_type', Branch::class)
            ->value('scope_id');

        if ($scopedBranchId) {
            $branch = Branch::find($scopedBranchId);
            if ($branch) {
                return $branch;
            }
        }

        return Branch::where('facility_type', 'like', '%Restaurant%')
            ->orWhere('name', 'like', '%Restaurant%')
            ->first() ?? Branch::first();
    }
}
