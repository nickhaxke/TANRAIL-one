<?php

namespace App\Domains\Modules\Restaurant\Models;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'business_unit_id',
        'user_id',
        'shift_id',
        'expense_date',
        'category',
        'amount',
        'paid_to',
        'payment_method',
        'receipt_number',
        'description',
        'status',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(RestaurantShift::class, 'shift_id');
    }
}
