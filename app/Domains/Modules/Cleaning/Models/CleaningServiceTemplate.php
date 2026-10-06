<?php

namespace App\Domains\Modules\Cleaning\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningServiceTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function serviceType()
    {
        return $this->belongsTo(CleaningServiceType::class, 'cleaning_service_type_id');
    }

    public function items()
    {
        return $this->hasMany(CleaningServiceTemplateItem::class, 'cleaning_service_template_id')->orderBy('sort_order');
    }
}
