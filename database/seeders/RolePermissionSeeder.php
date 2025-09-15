<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Product permissions
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Order permissions
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage orders',

            // Role & Permission permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view users',
            'view products',
            'create products',
            'edit products',
            'view categories',
            'create categories',
            'edit categories',
            'view orders',
            'edit orders',
            'manage orders',
        ]);

        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view products',
            'view categories',
            'create orders',
            'view orders',
        ]);

        // Create admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create manager user
        $manager = \App\Models\User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Create customer user
        $customer = \App\Models\User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $customer->assignRole('customer');
    }
}