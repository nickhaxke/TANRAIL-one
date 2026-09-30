<?php

namespace App\Domains\Core\Enums;

enum CreditNoteStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case APPLIED = 'applied';
    case VOIDED = 'voided';
}
