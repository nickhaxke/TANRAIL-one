<?php

namespace App\Domains\Modules\Cleaning\Handlers;

use App\Domains\Modules\Cleaning\Contracts\CleaningServiceHandlerInterface;
use Exception;

class CleaningServiceHandlerFactory
{
    /**
     * @var array<string, string>
     */
    protected array $handlers = [
        'facility_cleaning' => FacilityCleaningHandler::class,
        'sgr_coach_cleaning' => SgrCoachCleaningHandler::class,
        'fumigation' => FumigationHandler::class,
    ];

    public function make(string $serviceTypeCode): CleaningServiceHandlerInterface
    {
        if (! isset($this->handlers[$serviceTypeCode])) {
            throw new Exception("No handler configured for service type code: {$serviceTypeCode}");
        }

        $class = $this->handlers[$serviceTypeCode];

        return app($class);
    }

    public function registerHandler(string $serviceTypeCode, string $handlerClass): void
    {
        $this->handlers[$serviceTypeCode] = $handlerClass;
    }
}
