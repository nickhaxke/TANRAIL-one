<?php

namespace App\Domains\Core\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case CORPORATE = 'corporate';
}
