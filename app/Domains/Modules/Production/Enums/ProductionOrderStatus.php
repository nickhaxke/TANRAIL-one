<?php

namespace App\Domains\Modules\Production\Enums;

enum ProductionOrderStatus: string
{
    case DRAFT = 'DRAFT';
    case CONFIRMED = 'CONFIRMED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
