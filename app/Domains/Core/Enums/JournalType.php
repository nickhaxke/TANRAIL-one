<?php

namespace App\Domains\Core\Enums;

enum JournalType: string
{
    case OPERATIONAL = 'operational';
    case MANUAL = 'manual';
    case OPENING_BALANCE = 'opening_balance';
    case CLOSING = 'closing';
    case REVERSAL = 'reversal';
}
