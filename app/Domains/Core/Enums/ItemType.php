<?php

namespace App\Domains\Core\Enums;

enum ItemType: string
{
    case PHYSICAL = 'physical';
    case SERVICE = 'service';
    case PACKAGE = 'package';
}
