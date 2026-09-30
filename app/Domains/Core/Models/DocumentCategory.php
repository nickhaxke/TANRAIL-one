<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
