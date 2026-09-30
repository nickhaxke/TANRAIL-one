<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Processed Events (System Activity & Idempotency)
        $processedEventsCount = ProcessedEvent::whereBetween('created_at', [$startDateTime, $endDateTime])->count();
        $recentEvents = ProcessedEvent::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $totalUsers = User::count();
        $activeUsers = User::where('updated_at', '>=', Carbon::now()->subDays(7))->count();
        $totalRoles = Role::count();
        $totalBusinessUnits = BusinessUnit::count();
        $totalBranches = Branch::count();

        // Administrative Governance Readiness Score
        $unitsWithManagers = BusinessUnit::whereNotNull('manager_user_id')->count();
        $branchesWithSupervisors = Branch::whereNotNull('manager_user_id')->count();
        $totalEntities = $totalBusinessUnits + $totalBranches;
        $governanceScore = $totalEntities > 0
            ? (int) round((($unitsWithManagers + $branchesWithSupervisors) / $totalEntities) * 100)
            : 100;

        return view('management.audit.index', compact(
            'startDate',
            'endDate',
            'processedEventsCount',
            'recentEvents',
            'totalUsers',
            'activeUsers',
            'totalRoles',
            'totalBusinessUnits',
            'totalBranches',
            'governanceScore'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $events = ProcessedEvent::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->orderByDesc('id')
            ->get();

        $filename = "tanrail_audit_logs_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Record ID', 'Event Class / Action', 'Reference / Idempotency Key', 'Timestamp']);

            if ($events->isEmpty()) {
                fputcsv($handle, ['-', 'No events recorded in the selected period', '-', '-']);
            } else {
                foreach ($events as $event) {
                    fputcsv($handle, [
                        $event->id,
                        $event->event_class,
                        $event->reference_id,
                        $event->created_at ? Carbon::parse($event->created_at)->format('Y-m-d H:i:s') : 'N/A',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
