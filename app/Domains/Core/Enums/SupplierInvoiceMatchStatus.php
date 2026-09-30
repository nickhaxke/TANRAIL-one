<?php

namespace App\Domains\Core\Enums;

enum SupplierInvoiceMatchStatus: string
{
    case UNMATCHED = 'unmatched';
    case MATCHED = 'matched';
    case DISCREPANCY = 'discrepancy';
}
