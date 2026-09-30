<?php

namespace App\Domains\Core\Models;

use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    protected static function newFactory()
    {
        return UnitFactory::new();
    }
}
