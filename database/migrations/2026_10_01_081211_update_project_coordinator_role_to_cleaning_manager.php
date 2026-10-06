<?php

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
        $projectCoordinator = Role::where('name', 'Project Coordinator')->first();
        $cleaningManager = Role::where('name', 'Cleaning Manager')->first();

        if ($projectCoordinator && $cleaningManager) {
            // Reassign all users from Project Coordinator to Cleaning Manager
            // (Assuming there are no collisions where a user has both, but updateOrInsert is safer if needed.
            // Since we just update, we might hit duplicate key if a user has both.
            // Let's use a collection to iterate and attach/detach)
            $usersWithCoordinator = DB::table('role_user')->where('role_id', $projectCoordinator->id)->get();

            foreach ($usersWithCoordinator as $userRole) {
                $hasManager = DB::table('role_user')
                    ->where('user_id', $userRole->user_id)
                    ->where('role_id', $cleaningManager->id)
                    ->exists();

                if (! $hasManager) {
                    DB::table('role_user')->insert([
                        'user_id' => $userRole->user_id,
                        'role_id' => $cleaningManager->id,
                        'scope_type' => $userRole->scope_type,
                        'scope_id' => $userRole->scope_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Remove project coordinator assignments and the role itself
            DB::table('role_user')->where('role_id', $projectCoordinator->id)->delete();
            $projectCoordinator->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create Project Coordinator mapping if necessary
    }
};
