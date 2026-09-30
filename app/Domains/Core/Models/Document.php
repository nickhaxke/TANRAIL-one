<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'document_category_id',
        'title',
        'description',
        'file_path',
        'version',
        'status', // draft, published, archived
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }
}
