<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo permissions cho audits
        $auditPermissions = [
            'view audits',
            'delete audits',
        ];

        foreach ($auditPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Tạo hoặc lấy admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        // Tạo admin role nếu chưa có
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Gán tất cả permissions cho admin role
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // Gán admin role cho admin user
        $admin->assignRole($adminRole);

        $this->command->info('Admin permissions setup completed!');
        $this->command->info('Admin user: ' . $admin->name);
        $this->command->info('Total permissions: ' . $allPermissions->count());
    }
}
