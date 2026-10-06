<?php

namespace App\Domains\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Core\Traits\HasContextualRoles;
use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasContextualRoles, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function cleaningSupervisorAssignments()
    {
        return $this->hasMany(CleaningSupervisorAssignment::class, 'supervisor_id');
    }
}
