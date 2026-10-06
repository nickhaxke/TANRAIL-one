<?php

namespace App\Domains\Modules\Cleaning\Handlers;

use App\Domains\Modules\Cleaning\Models\WorkActivity;

class FumigationHandler implements CleaningServiceHandlerInterface
{
    /**
     * Define the operational structure for a fumigation service.
     */
    public function getBlueprint(): array
    {
        return [
            'name' => 'Fumigation Services',
            'code' => 'fumigation',
            'description' => 'Pest control and fumigation operations.',
            'requires_chemicals' => true,
            'requires_ppe' => true,
        ];
    }

    /**
     * Compute completion metrics for fumigation activities.
     */
    public function computeMetrics(WorkActivity $activity): array
    {
        $totalItems = $activity->items->count();
        if ($totalItems === 0) {
            return ['progress' => 0];
        }

        $completedItems = $activity->items->where('status', 'Completed')->count();
        $progress = round(($completedItems / $totalItems) * 100);

        return [
            'progress' => $progress,
            'total_areas' => $totalItems,
            'completed_areas' => $completedItems,
        ];
    }
}
