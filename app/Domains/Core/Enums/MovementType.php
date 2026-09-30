<?php

namespace App\Domains\Core\Enums;

enum MovementType: string
{
    case RECEIVE = 'receive';
    case ISSUE = 'issue';
    case TRANSFER = 'transfer';
    case ADJUST = 'adjust';
}
