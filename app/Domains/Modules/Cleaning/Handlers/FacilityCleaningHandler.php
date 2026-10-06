<?php

namespace App\Domains\Modules\Cleaning\Handlers;

use App\Domains\Modules\Cleaning\Contracts\CleaningServiceHandlerInterface;
use App\Domains\Modules\Cleaning\Models\WorkActivity;

class FacilityCleaningHandler implements CleaningServiceHandlerInterface
{
    public function getServiceTypeCode(): string
    {
        return 'facility_cleaning';
    }

    public function getCompletionValidationRules(): array
    {
        return [
            'items.*.status' => 'required|in:Completed,Incomplete,Pending',
            // No strict target quantity enforcement for basic facility cleaning
            // Just need a status
        ];
    }

    public function getSummaryMetrics(WorkActivity $operation): array
    {
        $items = $operation->items;

        $totalItems = $items->count();
        $completedItems = $items->where('status', 'Completed')->count();

        return [
            'total_tasks' => $totalItems,
            'completed_tasks' => $completedItems,
            'completion_rate' => $totalItems > 0 ? round(($completedItems / $totalItems) * 100).'%' : '0%',
        ];
    }
}
