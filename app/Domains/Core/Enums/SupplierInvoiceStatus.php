<?php

namespace App\Domains\Core\Enums;

enum SupplierInvoiceStatus: string
{
    case DRAFT = 'draft';
    case RECEIVED = 'received';
    case APPROVED = 'approved';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case VOIDED = 'voided';
}
