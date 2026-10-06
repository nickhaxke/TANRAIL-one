<?php

namespace Database\Seeders;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleaningFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::firstOrCreate(
            ['code' => 'TANRAIL'],
            ['name' => 'TANRAIL Investments Limited', 'status' => true]
        );

        // 1. Create Cleaning Business Unit
        $cleaningBu = BusinessUnit::firstOrCreate(
            ['code' => 'BU-CLN-01'],
            [
                'organization_id' => $org->id,
                'name' => 'TANRAIL Cleaning & Facility Services',
                'category' => 'Cleaning Operations',
                'status' => true,
                'description' => 'Station and train cleaning, facility management, and supervision.',
            ]
        );

        // 2. Create Cleaning Roles
        $cleaningManagerRole = Role::firstOrCreate(
            ['name' => 'Cleaning Manager'],
            ['description' => 'Manager of Cleaning Operations']
        );

        $cleaningSupervisorRole = Role::firstOrCreate(
            ['name' => 'Cleaning Supervisor'],
            ['description' => 'Supervisor of field cleaning operations']
        );

        // 3. Create a Demo Supervisor User
        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@cleaning.tanrail.co.tz'],
            [
                'name' => 'John Doe (Cleaning Supervisor)',
                'password' => Hash::make('password'),
            ]
        );

        // Assign role scoped to Organization (as roles are currently scoped in DatabaseSeeder)
        $alreadyHasRole = $supervisorUser->roles()
            ->wherePivot('role_id', $cleaningSupervisorRole->id)
            ->wherePivot('scope_type', Organization::class)
            ->wherePivot('scope_id', $org->id)
            ->exists();

        if (! $alreadyHasRole) {
            $supervisorUser->assignRole($cleaningSupervisorRole, Organization::class, $org->id);
        }

        $this->command->info('Cleaning Foundation seeded successfully.');
    }
}
