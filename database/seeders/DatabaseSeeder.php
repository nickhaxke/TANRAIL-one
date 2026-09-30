<?php

namespace Database\Seeders;

use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // 1. Create Organization
        $org = Organization::firstOrCreate(
            ['code' => 'TANRAIL'],
            [
                'name' => 'TANRAIL Investments Limited',
                'trading_name' => 'TANRAIL',
                'status' => true,
                'country' => 'Tanzania',
                'city' => 'Dar es Salaam',
                'address' => 'SGR Central Station, Gerezani, Ilala',
                'email' => 'corporate@tanrail.co.tz',
                'phone' => '+255 22 211 0579',
                'website' => 'https://tanrail.co.tz',
            ]
        );

        // 2. Create Admin User
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $admin = User::firstOrCreate(
            ['email' => 'admin@tanrail.co.tz'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );

        if ($superAdminRole) {
            $admin->assignRole($superAdminRole, Organization::class, $org->id);
        }
    }
}
