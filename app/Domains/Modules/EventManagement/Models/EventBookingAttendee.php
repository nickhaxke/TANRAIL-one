<?php

namespace App\Domains\Modules\EventManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBookingAttendee extends Model
{
    use HasFactory;

    protected $table = 'event_booking_attendees';

    protected $fillable = [
        'event_booking_id',
        'name',
        'email',
        'phone',
        'vip_status',
        'notes',
    ];

    protected $casts = [
        'vip_status' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(EventBooking::class, 'event_booking_id');
    }
}
