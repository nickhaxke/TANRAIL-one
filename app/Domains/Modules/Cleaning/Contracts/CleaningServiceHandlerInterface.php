<?php

namespace App\Domains\Modules\Cleaning\Contracts;

use App\Domains\Modules\Cleaning\Models\WorkActivity;

interface CleaningServiceHandlerInterface
{
    /**
     * Get the service type code this handler is responsible for.
     */
    public function getServiceTypeCode(): string;

    /**
     * Define any specific validation rules for completing the work activity items.
     */
    public function getCompletionValidationRules(): array;

    /**
     * Return summary metrics for the operation.
     */
    public function getSummaryMetrics(WorkActivity $operation): array;
}
