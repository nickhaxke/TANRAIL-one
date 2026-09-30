<?php

namespace App\Domains\Core\DTOs;

use App\Domains\Core\Enums\SupplierInvoiceMatchStatus;

class MatchResult
{
    /**
     * @param  array<int, string>  $discrepancies
     * @param  array<int, array<string, mixed>>  $lineDetails
     */
    public function __construct(
        public readonly SupplierInvoiceMatchStatus $status,
        public readonly bool $isMatched,
        public readonly array $discrepancies,
        public readonly array $lineDetails,
        public readonly string $summary
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'is_matched' => $this->isMatched,
            'discrepancies' => $this->discrepancies,
            'line_details' => $this->lineDetails,
            'summary' => $this->summary,
        ];
    }
}
