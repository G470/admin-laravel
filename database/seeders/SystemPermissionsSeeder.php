<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permission groups
        $permissionGroups = [
            'Admin Management' => [
                'admin.dashboard.view' => 'View admin dashboard',
                'admin.users.view' => 'View users list',
                'admin.users.create' => 'Create new users',
                'admin.users.edit' => 'Edit user details',
                'admin.users.delete' => 'Delete users',
                'admin.roles.view' => 'View roles list',
                'admin.roles.create' => 'Create custom roles',
                'admin.roles.edit' => 'Edit custom roles',
                'admin.roles.delete' => 'Delete custom roles',
                'admin.permissions.assign' => 'Assign permissions to roles',
                'admin.settings.view' => 'View system settings',
                'admin.settings.edit' => 'Modify system settings',
                'admin.reports.view' => 'View system reports',
                'admin.logs.view' => 'View system logs',
            ],

            'Vendor Management' => [
                'vendor.dashboard.view' => 'View vendor dashboard',
                'vendor.profile.view' => 'View vendor profile',
                'vendor.profile.edit' => 'Edit vendor profile',
                'vendor.rentals.view' => 'View rental listings',
                'vendor.rentals.create' => 'Create rental listings',
                'vendor.rentals.edit' => 'Edit rental listings',
                'vendor.rentals.delete' => 'Delete rental listings',
                'vendor.bookings.view' => 'View bookings',
                'vendor.bookings.manage' => 'Manage bookings',
                'vendor.statistics.view' => 'View vendor statistics',
                'vendor.payments.view' => 'View payment history',
                'vendor.credits.view' => 'View credit packages',
                'vendor.credits.purchase' => 'Purchase credit packages',
            ],

            'Content Management' => [
                'content.view' => 'View content',
                'content.create' => 'Create content',
                'content.edit' => 'Edit own content',
                'content.edit.all' => 'Edit all content',
                'content.delete' => 'Delete content',
                'content.publish' => 'Publish content',
                'content.moderate' => 'Moderate content',
            ],

            'System Operations' => [
                'system.backup' => 'Create system backups',
                'system.maintenance' => 'Enable maintenance mode',
                'system.cache.clear' => 'Clear system cache',
                'system.logs.clear' => 'Clear system logs',
            ]
        ];

        // Create permissions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['description' => $description, 'group' => $group]
                );
            }
        }

        // Assign permissions to protected roles
        $this->assignProtectedRolePermissions();

        $this->command->info('System permissions seeded successfully.');
    }

    private function assignProtectedRolePermissions(): void
    {
        try {
            // Admin gets all permissions
            $adminRole = Role::findByName('admin', 'web');
            if ($adminRole) {
                $adminRole->givePermissionTo(Permission::all());
            }

            // Vendor gets vendor-specific permissions
            $vendorRole = Role::findByName('vendor', 'web');
            if ($vendorRole) {
                $vendorPermissions = Permission::where('name', 'like', 'vendor.%')->get();
                $vendorRole->givePermissionTo($vendorPermissions);
            }

            // User gets basic content permissions
            $userRole = Role::findByName('user', 'web');
            if ($userRole) {
                $userPermissions = Permission::whereIn('name', [
                    'content.view',
                    'content.create',
                    'content.edit'
                ])->get();
                $userRole->givePermissionTo($userPermissions);
            }

            // Guest gets only view permissions
            $guestRole = Role::findByName('guest', 'web');
            if ($guestRole) {
                $guestPermissions = Permission::where('name', 'like', '%.view')->get();
                $guestRole->givePermissionTo($guestPermissions);
            }
        } catch (\Exception $e) {
            $this->command->warn('Could not assign permissions to some roles: ' . $e->getMessage());
        }
    }
}
