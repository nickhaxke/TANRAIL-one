<?php

namespace App\Domains\Modules\Cleaning\Models;

use Illuminate\Database\Eloquent\Model;

class WorkActivityItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'target_qty' => 'decimal:2',
        'done_qty' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attributes' => 'array',
    ];

    public function workActivity()
    {
        return $this->belongsTo(WorkActivity::class, 'work_activity_id');
    }

    public function templateItem()
    {
        return $this->belongsTo(CleaningServiceTemplateItem::class, 'cleaning_service_template_item_id');
    }
}
