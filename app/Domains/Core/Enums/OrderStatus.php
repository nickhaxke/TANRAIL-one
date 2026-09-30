<?php

namespace App\Domains\Core\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case FULFILLED = 'fulfilled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
