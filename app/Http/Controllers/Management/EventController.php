<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Modules\EventManagement\Models\EventBooking;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->addMonths(3)->endOfMonth()->toDateString()); // Events are often future-looking

        $organizations = Organization::all();
        $businessUnits = $orgId ? BusinessUnit::where('organization_id', $orgId)->get() : BusinessUnit::all();

        $eventQuery = EventBooking::whereBetween('start_date_time', [$startDate, $endDate]);

        if ($buId) {
            $eventQuery->where('business_unit_id', $buId);
        } elseif ($orgId) {
            $eventQuery->where('organization_id', $orgId);
        }

        // KPIs
        $upcomingEvents = (clone $eventQuery)->where('start_date_time', '>=', now())->count();
        $confirmedEvents = (clone $eventQuery)->where('status', 'confirmed')->count();

        $totalRevenue = (clone $eventQuery)->whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalDeposits = (clone $eventQuery)->sum('deposit_paid_amount');
        $outstandingBalance = $totalRevenue - $totalDeposits; // Simplification, as paid final invoices aren't included here directly unless we join Invoice

        // Upcoming Events List
        $recentEvents = (clone $eventQuery)->with(['customer', 'businessUnit'])
            ->orderBy('start_date_time', 'asc')
            ->limit(10)
            ->get();

        return view('management.events.index', compact(
            'organizations', 'businessUnits',
            'orgId', 'buId', 'startDate', 'endDate',
            'upcomingEvents', 'confirmedEvents',
            'totalRevenue', 'totalDeposits', 'outstandingBalance',
            'recentEvents'
        ));
    }
}
