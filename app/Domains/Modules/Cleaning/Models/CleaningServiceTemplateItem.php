<?php

namespace App\Domains\Modules\Cleaning\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningServiceTemplateItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_required' => 'boolean',
        'default_target_qty' => 'decimal:2',
    ];

    public function template()
    {
        return $this->belongsTo(CleaningServiceTemplate::class, 'cleaning_service_template_id');
    }
}
