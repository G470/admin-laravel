<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ProtectedRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Protected system roles - Update existing with additional fields
        $protectedRoles = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'description' => 'System Administrator - Full Access',
                'is_protected' => true,
                'color' => '#dc3545'
            ],
            [
                'name' => 'vendor',
                'guard_name' => 'web',
                'description' => 'Vendor Account - Business Access',
                'is_protected' => true,
                'color' => '#28a745'
            ],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'description' => 'Regular User - Basic Access',
                'is_protected' => true,
                'color' => '#007bff'
            ],
            [
                'name' => 'guest',
                'guard_name' => 'web',
                'description' => 'Guest User - Limited Access',
                'is_protected' => true,
                'color' => '#6c757d'
            ]
        ];

        foreach ($protectedRoles as $roleData) {
            // Find existing role or create new one
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                $roleData
            );

            // Update existing roles with new fields (only if they don't have them)
            if (!$role->description) {
                $role->update([
                    'description' => $roleData['description'],
                    'is_protected' => $roleData['is_protected'],
                    'color' => $roleData['color']
                ]);
            }
        }

        $this->command->info('Protected roles seeded successfully.');
    }
}
