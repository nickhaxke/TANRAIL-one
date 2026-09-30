<?php

namespace App\Domains\Core\Models;

use Database\Factories\BusinessUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'status',
        'description',
        'category',
        'cost_center',
        'manager_user_id',
        'manager_name',
        'manager_email',
        'manager_phone',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function newFactory()
    {
        return BusinessUnitFactory::new();
    }
}
