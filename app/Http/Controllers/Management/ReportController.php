<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $totalBusinessUnits = BusinessUnit::count();
        $unitsWithManagers = BusinessUnit::whereNotNull('manager_user_id')->count();

        $totalBranches = Branch::count();
        $branchesWithSupervisors = Branch::whereNotNull('manager_user_id')->count();

        $totalStaff = User::count();
        $totalRoles = Role::count();

        $regionsCount = Branch::whereNotNull('city')->distinct('city')->count('city');
        if ($regionsCount === 0 && $totalBranches > 0) {
            $regionsCount = 1;
        }

        $totalEntities = $totalBusinessUnits + $totalBranches;
        $governanceScore = $totalEntities > 0
            ? (int) round((($unitsWithManagers + $branchesWithSupervisors) / $totalEntities) * 100)
            : 100;

        return view('management.reports.index', compact(
            'totalBusinessUnits',
            'unitsWithManagers',
            'totalBranches',
            'branchesWithSupervisors',
            'totalStaff',
            'totalRoles',
            'regionsCount',
            'governanceScore'
        ));
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $timestamp = Carbon::now()->format('Y_m_d_His');

        return match ($type) {
            'divisions' => $this->exportDivisions($timestamp),
            'stations' => $this->exportStations($timestamp),
            'staff' => $this->exportStaff($timestamp),
            'governance' => $this->exportGovernance($timestamp),
            default => abort(404, 'Report not found.'),
        };
    }

    private function exportDivisions(string $timestamp): StreamedResponse
    {
        $units = BusinessUnit::with(['managerUser', 'branches'])->orderBy('name')->get();
        $filename = "tanrail_divisions_directory_{$timestamp}.csv";

        return response()->streamDownload(function () use ($units) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['ID', 'Division Code', 'Division Name', 'Assigned Manager', 'Manager Email', 'Total Stations/Branches', 'Operational Status', 'Established Date']);

            foreach ($units as $unit) {
                fputcsv($handle, [
                    $unit->id,
                    $unit->code ?? 'N/A',
                    $unit->name,
                    $unit->managerUser?->name ?? 'Unassigned Manager',
                    $unit->managerUser?->email ?? '-',
                    $unit->branches->count(),
                    $unit->status ? 'Active' : 'Inactive',
                    $unit->created_at ? Carbon::parse($unit->created_at)->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportStations(string $timestamp): StreamedResponse
    {
        $branches = Branch::with(['businessUnit', 'managerUser'])->orderBy('name')->get();
        $filename = "tanrail_stations_network_{$timestamp}.csv";

        return response()->streamDownload(function () use ($branches) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Station ID', 'Station Code', 'Station Name', 'Parent Division', 'Region / Location', 'Assigned Supervisor', 'Supervisor Email', 'Operational Status', 'Registered Date']);

            foreach ($branches as $branch) {
                fputcsv($handle, [
                    $branch->id,
                    $branch->code ?? 'N/A',
                    $branch->name,
                    $branch->businessUnit?->name ?? 'HQ / General',
                    $branch->city ?? 'Default Corridor',
                    $branch->managerUser?->name ?? 'Unassigned Supervisor',
                    $branch->managerUser?->email ?? '-',
                    $branch->status ? 'Active' : 'Inactive',
                    $branch->created_at ? Carbon::parse($branch->created_at)->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportStaff(string $timestamp): StreamedResponse
    {
        $users = User::with(['roles'])->orderBy('name')->get();
        $filename = "tanrail_staff_roster_{$timestamp}.csv";

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Staff ID', 'Full Name', 'Official Email', 'Assigned Roles', 'Registered Date']);

            foreach ($users as $user) {
                $roleNames = $user->roles->pluck('name')->implode(', ');
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $roleNames ?: 'General Staff',
                    $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportGovernance(string $timestamp): StreamedResponse
    {
        $units = BusinessUnit::with('managerUser')->get();
        $branches = Branch::with(['businessUnit', 'managerUser'])->get();
        $filename = "tanrail_governance_compliance_{$timestamp}.csv";

        return response()->streamDownload(function () use ($units, $branches) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Level', 'Entity Code', 'Entity Name', 'Manager / Supervisor Appointed', 'Leader Contact', 'Compliance Status']);

            foreach ($units as $unit) {
                fputcsv($handle, [
                    'Division (Business Unit)',
                    $unit->code ?? 'N/A',
                    $unit->name,
                    $unit->managerUser ? $unit->managerUser->name : 'NONE (Missing Director)',
                    $unit->managerUser ? $unit->managerUser->email : '-',
                    $unit->managerUser ? 'COMPLIANT' : 'NON-COMPLIANT (Action Required)',
                ]);
            }

            foreach ($branches as $branch) {
                fputcsv($handle, [
                    'Station / Branch',
                    $branch->code ?? 'N/A',
                    $branch->name.' ('.$branch->city.')',
                    $branch->managerUser ? $branch->managerUser->name : 'NONE (Missing Supervisor)',
                    $branch->managerUser ? $branch->managerUser->email : '-',
                    $branch->managerUser ? 'COMPLIANT' : 'NON-COMPLIANT (Action Required)',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
