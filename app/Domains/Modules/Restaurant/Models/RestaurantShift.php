<?php

namespace App\Domains\Modules\Restaurant\Models;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'opened_at',
        'closed_at',
        'status',
        'opening_float',
        'closing_cash_counted',
        'expected_cash',
        'cash_difference',
        'total_sales',
        'total_expenses',
        'total_orders_count',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_float' => 'decimal:2',
        'closing_cash_counted' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_orders_count' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shift_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(RestaurantExpense::class, 'shift_id');
    }

    public function getLiveCashSalesAttribute(): float
    {
        return (float) $this->orders()
            ->withoutGlobalScopes()
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.method', 'cash')
            ->sum('payments.amount');
    }

    public function getLiveCashExpensesAttribute(): float
    {
        return (float) $this->expenses()
            ->where('payment_method', 'cash')
            ->sum('amount');
    }

    public function calculateExpectedCash(): float
    {
        return (float) ($this->opening_float + $this->live_cash_sales - $this->live_cash_expenses);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
