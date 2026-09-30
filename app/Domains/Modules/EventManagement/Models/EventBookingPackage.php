<?php

namespace App\Domains\Modules\EventManagement\Models;

use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\TaxCategory;
use App\Domains\Core\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBookingPackage extends Model
{
    use HasFactory;

    protected $table = 'event_booking_packages';

    protected $fillable = [
        'event_booking_id',
        'item_id',
        'unit_id',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_category_id',
        'tax_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(EventBooking::class, 'event_booking_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }
}
