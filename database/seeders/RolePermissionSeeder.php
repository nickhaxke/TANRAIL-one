<?php

namespace Database\Seeders;

use App\Domains\Core\Models\Permission;
use App\Domains\Core\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Organization
            ['name' => 'organization.view', 'description' => 'View Enterprise Profile and Corporate Details'],
            ['name' => 'organization.manage', 'description' => 'Update Statutory Credentials, TRA TIN, and Headquarters'],

            // Business Units
            ['name' => 'business_units.view', 'description' => 'View Commercial Business Units and Division Topology'],
            ['name' => 'business_units.create', 'description' => 'Register New Commercial Business Units'],
            ['name' => 'business_units.edit', 'description' => 'Configure Division Settings and Assign Unit Managers'],
            ['name' => 'business_units.delete', 'description' => 'Deactivate or Delete Commercial Business Units'],

            // Branches & Outlets
            ['name' => 'branches.view', 'description' => 'View Station Counters, Outlets and Catering Hubs'],
            ['name' => 'branches.create', 'description' => 'Register New Station Branches and Outlets'],
            ['name' => 'branches.edit', 'description' => 'Update Branch Details and Assign Station Supervisors'],
            ['name' => 'branches.delete', 'description' => 'Deactivate or Delete Station Branches'],

            // Staff & User Management
            ['name' => 'users.view', 'description' => 'View Staff Directory and Operational Assignments'],
            ['name' => 'users.manage', 'description' => 'Create Staff Accounts and Assign Institutional Roles'],

            // Roles & Permissions
            ['name' => 'roles.view', 'description' => 'View Roles and Access Matrix'],
            ['name' => 'roles.manage', 'description' => 'Configure Roles and Assign Statutory Permissions'],

            // Audit
            ['name' => 'audit.view', 'description' => 'Inspect Enterprise Event Logs and Compliance Trail'],

            // Restaurant Operations
            ['name' => 'pos.access', 'description' => 'Access Station Restaurant Point of Sale terminal'],
            ['name' => 'pos.sell', 'description' => 'Process Customer Orders and Receive Payments'],
            ['name' => 'kitchen.view', 'description' => 'Access Station Kitchen Display System (KDS)'],
            ['name' => 'restaurant.manage', 'description' => 'Manage Restaurant Menu, Station Shifts, and Daily Cash'],

            // Cleaning Operations & Governance
            ['name' => 'cleaning.dashboard.view', 'description' => 'View Cleaning Module Operational Dashboard'],
            ['name' => 'cleaning.operations.manage', 'description' => 'Conduct Daily Control, Workforce Check, Activities, and Issues at Assigned Station'],
            ['name' => 'cleaning.operations.monitor', 'description' => 'Monitor All Branch Daily Operations and Station Supervisors'],
            ['name' => 'cleaning.workers.manage', 'description' => 'Create, Edit, and Assign Cleaning Workers to Stations and Supervisors'],
            ['name' => 'cleaning.workers.view', 'description' => 'View Assigned Cleaning Workers and Station Workforce'],
            ['name' => 'cleaning.store.view', 'description' => 'View Central Cleaning Store Balances and Movements'],
            ['name' => 'cleaning.store.receive', 'description' => 'Receive Stock into Central Cleaning Store'],
            ['name' => 'cleaning.store.issue', 'description' => 'Issue Stock from Central Cleaning Store'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $perm) {
            $createdPermissions[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        // 1. Super Admin / Executive Admin
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Full administrative sovereignty over all TANRAIL systems, divisions and outlets']
        );
        $superAdmin->permissions()->sync(array_values(array_map(fn ($p) => $p->id, $createdPermissions)));

        // 2. Business Unit Director
        $buDirector = Role::firstOrCreate(
            ['name' => 'Business Unit Director'],
            ['description' => 'Executive control over an assigned commercial division and its branches']
        );
        $buDirectorPerms = [
            $createdPermissions['organization.view']->id,
            $createdPermissions['business_units.view']->id,
            $createdPermissions['branches.view']->id,
            $createdPermissions['branches.create']->id,
            $createdPermissions['branches.edit']->id,
            $createdPermissions['users.view']->id,
        ];
        $buDirector->permissions()->sync($buDirectorPerms);

        // 3. Station Supervisor
        $stationSupervisor = Role::firstOrCreate(
            ['name' => 'Station Supervisor'],
            ['description' => 'Operational lead at a specific railway station counter or service outlet']
        );
        $stationSupervisorPerms = [
            $createdPermissions['branches.view']->id,
            $createdPermissions['users.view']->id,
            $createdPermissions['pos.access']->id,
            $createdPermissions['restaurant.manage']->id,
            $createdPermissions['cleaning.dashboard.view']->id,
            $createdPermissions['cleaning.operations.manage']->id,
            $createdPermissions['cleaning.workers.view']->id,
        ];
        $stationSupervisor->permissions()->sync($stationSupervisorPerms);

        // 4. Audit & Compliance Officer
        $auditor = Role::firstOrCreate(
            ['name' => 'Audit & Compliance Officer'],
            ['description' => 'Read-only access for statutory governance, review, and verification']
        );
        $auditorPerms = [
            $createdPermissions['organization.view']->id,
            $createdPermissions['business_units.view']->id,
            $createdPermissions['branches.view']->id,
            $createdPermissions['users.view']->id,
            $createdPermissions['audit.view']->id,
        ];
        $auditor->permissions()->sync($auditorPerms);

        // 5. Restaurant Manager
        $restaurantManager = Role::firstOrCreate(
            ['name' => 'Restaurant Manager'],
            ['description' => 'Supervises station dining facility, menu catalog, shifts, and daily service']
        );
        $restaurantManager->permissions()->sync([
            $createdPermissions['pos.access']->id,
            $createdPermissions['pos.sell']->id,
            $createdPermissions['kitchen.view']->id,
            $createdPermissions['restaurant.manage']->id,
            $createdPermissions['branches.view']->id,
        ]);

        // 6. Restaurant Cashier
        $cashier = Role::firstOrCreate(
            ['name' => 'Restaurant Cashier'],
            ['description' => 'Handles customer orders, station cash register, and electronic payments']
        );
        $cashier->permissions()->sync([
            $createdPermissions['pos.access']->id,
            $createdPermissions['pos.sell']->id,
        ]);

        // 7. Kitchen Staff / Chef
        $kitchenStaff = Role::firstOrCreate(
            ['name' => 'Kitchen Staff'],
            ['description' => 'Prepares meal orders via the station Kitchen Display System (KDS)']
        );
        $kitchenStaff->permissions()->sync([
            $createdPermissions['kitchen.view']->id,
        ]);

        // 8. Waiter / Service Crew
        $waiter = Role::firstOrCreate(
            ['name' => 'Waiter'],
            ['description' => 'Station table service and mobile order taking']
        );
        $waiter->permissions()->sync([
            $createdPermissions['pos.access']->id,
            $createdPermissions['pos.sell']->id,
        ]);

        // 9. Cleaning Manager
        $cleaningManager = Role::firstOrCreate(
            ['name' => 'Cleaning Manager'],
            ['description' => 'Executive control over Cleaning Business Unit, workers, and operations']
        );
        $cleaningManager->permissions()->sync([
            $createdPermissions['cleaning.dashboard.view']->id,
            $createdPermissions['cleaning.operations.monitor']->id,
            $createdPermissions['cleaning.workers.manage']->id,
            $createdPermissions['cleaning.workers.view']->id,
            $createdPermissions['cleaning.store.view']->id,
            $createdPermissions['users.view']->id,
            $createdPermissions['branches.view']->id,
        ]);

        // 10. Store Keeper
        $storeKeeper = Role::firstOrCreate(
            ['name' => 'Store Keeper'],
            ['description' => 'Custodian of Central Cleaning Store and inventory fulfillment']
        );
        $storeKeeper->permissions()->sync([
            $createdPermissions['cleaning.dashboard.view']->id,
            $createdPermissions['cleaning.store.view']->id,
            $createdPermissions['cleaning.store.receive']->id,
            $createdPermissions['cleaning.store.issue']->id,
        ]);

    }
}
