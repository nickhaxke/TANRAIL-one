<?php

namespace App\Domains\Modules\Cleaning\Services;

use App\Domains\Modules\Cleaning\Models\DailyControl;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CleaningTimesheetService
{
    /**
     * Derive timesheet records from recorded attendance and work activities.
     */
    public function getDailyTimesheet(int $businessUnitId, ?int $branchId = null, ?string $date = null): Collection
    {
        $date = $date ?? today()->toDateString();

        $query = DailyControl::where('business_unit_id', $businessUnitId)
            ->whereDate('date', $date)
            ->with([
                'branch',
                'supervisor',
                'workerAttendances' => function ($q) {
                    $q->where('is_present', true)
                        ->with([
                            'cleaningWorker.currentBranch',
                            'workActivities' => function ($actQuery) {
                                $actQuery->with('location');
                            },
                        ]);
                },
            ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $controls = $query->get();
        $timesheetEntries = collect();

        foreach ($controls as $control) {
            foreach ($control->workerAttendances as $attendance) {
                $worker = $attendance->cleaningWorker;
                if (! $worker) {
                    continue;
                }

                $activities = $attendance->workActivities->map(function ($act) {
                    $durationMinutes = 0;
                    if ($act->started_at && $act->completed_at) {
                        $durationMinutes = Carbon::parse($act->started_at)->diffInMinutes(Carbon::parse($act->completed_at));
                    }

                    return [
                        'id' => $act->id,
                        'name' => $act->activity_name,
                        'location' => $act->location?->name ?? 'General',
                        'status' => $act->status,
                        'verification_status' => $act->verification_status,
                        'started_at' => $act->started_at ? Carbon::parse($act->started_at)->format('H:i') : null,
                        'completed_at' => $act->completed_at ? Carbon::parse($act->completed_at)->format('H:i') : null,
                        'duration_minutes' => $durationMinutes,
                        'duration_formatted' => sprintf('%02dh %02dm', intdiv($durationMinutes, 60), $durationMinutes % 60),
                    ];
                });

                $totalMinutes = null;
                $formattedWork = 'Incomplete';
                $workHours = null;

                if ($attendance->check_in_at && $attendance->check_out_at) {
                    $totalMinutes = Carbon::parse($attendance->check_in_at)->diffInMinutes(Carbon::parse($attendance->check_out_at));
                    $workHours = round($totalMinutes / 60, 2);
                    $formattedWork = sprintf('%02dh %02dm', intdiv($totalMinutes, 60), $totalMinutes % 60);
                }

                $timesheetEntries->push([
                    'worker_id' => $worker->id,
                    'worker_code' => $worker->worker_id,
                    'worker_name' => "{$worker->first_name} {$worker->last_name}",
                    'branch_id' => $control->branch_id,
                    'branch_name' => $control->branch?->name ?? 'Unknown',
                    'supervisor_name' => $control->supervisor?->name ?? 'Unassigned',
                    'date' => $control->date->toDateString(),
                    'arrival_time' => $attendance->check_in_at ? Carbon::parse($attendance->check_in_at)->format('H:i') : ($attendance->arrival_time ?? '--:--'),
                    'check_in_at' => $attendance->check_in_at,
                    'check_out_at' => $attendance->check_out_at,
                    'status' => $attendance->status,
                    'is_present' => $attendance->is_present,
                    'total_activities' => $activities->count(),
                    'completed_activities' => $activities->where('status', 'Completed')->count(),
                    'total_work_minutes' => $totalMinutes,
                    'total_work_hours' => $workHours,
                    'total_work_formatted' => $formattedWork,
                    'activities' => $activities,
                ]);
            }
        }

        return $timesheetEntries;
    }
}
