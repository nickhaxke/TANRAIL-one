<?php

namespace App\Domains\Modules\EventManagement\Enums;

enum EventBookingStatus: string
{
    case DRAFT = 'DRAFT';
    case QUOTED = 'QUOTED';
    case CONFIRMED = 'CONFIRMED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
