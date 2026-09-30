<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class ProcessedEvent extends Model
{
    protected $fillable = [
        'event_class',
        'reference_id',
    ];

    public $timestamps = false;

    public static function process(string $eventClass, string $referenceId, callable $callback)
    {
        return DB::transaction(function () use ($eventClass, $referenceId, $callback) {
            try {
                // If it already exists, this insert will fail with UniqueConstraintViolationException
                static::create([
                    'event_class' => $eventClass,
                    'reference_id' => $referenceId,
                ]);

                return $callback();
            } catch (UniqueConstraintViolationException $e) {
                // Event was already processed, idempotent return
                return null;
            }
        });
    }
}
