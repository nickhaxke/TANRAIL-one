<?php

use App\Domains\Core\Models\Permission;
use App\Domains\Core\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Create the new Cleaning Supervisor role
            $cleaningSupervisor = Role::firstOrCreate(
                ['name' => 'Cleaning Supervisor'],
                ['description' => 'Supervisor of field cleaning operations']
            );

            // 2. Define the cleaning permissions
            $permissionNames = [
                'branches.view',
                'users.view',
                'cleaning.dashboard.view',
                'cleaning.operations.manage',
                'cleaning.workers.view',
            ];

            $permissionIds = [];
            foreach ($permissionNames as $name) {
                // Ensure the permission exists (important for fresh test DBs where seeders run after migrations)
                $perm = Permission::firstOrCreate(
                    ['name' => $name],
                    ['description' => 'Created by migration for '.$name]
                );
                $permissionIds[] = $perm->id;
            }

            $cleaningSupervisor->permissions()->syncWithoutDetaching($permissionIds);

            // 3. Find the old Station Supervisor role
            $stationSupervisor = Role::where('name', 'Station Supervisor')->first();

            if ($stationSupervisor) {
                // 4. Migrate existing users from Station Supervisor to Cleaning Supervisor
                $assignments = DB::table('role_user')->where('role_id', $stationSupervisor->id)->get();

                foreach ($assignments as $assignment) {
                    DB::table('role_user')->updateOrInsert(
                        [
                            'user_id' => $assignment->user_id,
                            'role_id' => $cleaningSupervisor->id,
                            'scope_type' => $assignment->scope_type,
                            'scope_id' => $assignment->scope_id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // 5. Remove cleaning-specific permissions from Station Supervisor to complete the split
                $cleaningOnlyPerms = Permission::whereIn('name', [
                    'cleaning.dashboard.view',
                    'cleaning.operations.manage',
                    'cleaning.workers.view',
                ])->pluck('id')->toArray();

                $stationSupervisor->permissions()->detach($cleaningOnlyPerms);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $cleaningSupervisor = Role::where('name', 'Cleaning Supervisor')->first();
            $stationSupervisor = Role::where('name', 'Station Supervisor')->first();

            if ($cleaningSupervisor && $stationSupervisor) {
                // Restore cleaning permissions back to Station Supervisor
                $cleaningOnlyPerms = Permission::whereIn('name', [
                    'cleaning.dashboard.view',
                    'cleaning.operations.manage',
                    'cleaning.workers.view',
                ])->pluck('id')->toArray();

                $stationSupervisor->permissions()->syncWithoutDetaching($cleaningOnlyPerms);
            }

            // Note: We don't delete the new role or role assignments in down() to avoid data loss
            // if it was assigned to new users independently.
        });
    }
};
