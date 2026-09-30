<?php

namespace App\Domains\Core\Enums;

enum TransferStatus: string
{
    case PENDING = 'pending';
    case IN_TRANSIT = 'in_transit';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
