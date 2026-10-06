<?php

namespace App\Domains\Core\Models;

use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'name',
        'code',
        'status',
        'address',
        'facility_type',
        'city',
        'phone',
        'email',
        'manager_user_id',
        'manager_name',
        'default_sales_location_id',
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function defaultSalesLocation()
    {
        return $this->belongsTo(InventoryLocation::class, 'default_sales_location_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function cleaningSupervisorAssignments()
    {
        return $this->hasMany(CleaningSupervisorAssignment::class, 'branch_id');
    }

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function newFactory()
    {
        return BranchFactory::new();
    }
}
