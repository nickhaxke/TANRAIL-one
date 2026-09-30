<?php

namespace App\Domains\Core\Enums;

enum JournalStatus: string
{
    case POSTED = 'posted';
    case REVERSED = 'reversed';
}
