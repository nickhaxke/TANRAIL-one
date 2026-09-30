<?php

namespace App\Domains\Core\Enums;

enum PurchaseOrderStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case PARTIAL_RECEIVED = 'partial_received';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';
}
