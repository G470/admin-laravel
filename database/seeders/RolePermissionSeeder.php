<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Create permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage rentals',
            'manage categories',
            'manage reviews',
            'manage settings',
            'rent items',
            'list items',
            'view reports',
            'manage payments'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Vendor gets specific permissions
        $vendorRole->givePermissionTo([
            'view dashboard',
            'list items',
            'manage rentals',
            'view reports'
        ]);

        // User gets basic permissions
        $userRole->givePermissionTo([
            'rent items'
        ]);
    }
}
