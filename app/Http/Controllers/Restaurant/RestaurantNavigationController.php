<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\PurchaseOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantNavigationController extends Controller
{
    public function placeholder(Request $request)
    {
        $pageTitle = $request->route()->getName();
        // Convert something like "restaurant.purchasing.suppliers" to "Suppliers"
        $parts = explode('.', $pageTitle);
        $title = ucwords(str_replace(['-', '_'], ' ', end($parts)));

        if ($pageTitle === 'restaurant.purchasing.requests') {
            $requests = PurchaseOrder::where('created_by', auth()->id())->orderBy('created_at', 'desc')->get();

            return view('restaurant.purchasing.requests', compact('title', 'requests'));
        }

        return view('restaurant.placeholder', compact('title'));
    }
}
