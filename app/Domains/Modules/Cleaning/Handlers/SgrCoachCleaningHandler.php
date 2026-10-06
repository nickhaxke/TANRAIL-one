<?php

namespace App\Domains\Modules\Cleaning\Handlers;

use App\Domains\Modules\Cleaning\Contracts\CleaningServiceHandlerInterface;
use App\Domains\Modules\Cleaning\Models\WorkActivity;

class SgrCoachCleaningHandler implements CleaningServiceHandlerInterface
{
    public function getServiceTypeCode(): string
    {
        return 'sgr_coach_cleaning';
    }

    public function getCompletionValidationRules(): array
    {
        return [
            'items.*.status' => 'required|in:Completed,Incomplete,Pending',
            'items.*.done_qty' => 'required_if:items.*.status,Completed|numeric|min:0',
        ];
    }

    public function getSummaryMetrics(WorkActivity $operation): array
    {
        $items = $operation->items;

        $totalItems = $items->count();
        $completedItems = $items->where('status', 'Completed')->count();

        $totalTargetUnits = $items->sum('target_qty');
        $totalDoneUnits = $items->where('status', 'Completed')->sum('done_qty');

        return [
            'total_tasks' => $totalItems,
            'completed_tasks' => $completedItems,
            'completion_rate' => $totalItems > 0 ? round(($completedItems / $totalItems) * 100).'%' : '0%',
            'total_target_units' => $totalTargetUnits,
            'total_done_units' => $totalDoneUnits,
        ];
    }
}
