<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin-User
        $admin = User::updateOrCreate(
            ['email' => 'admin@inlando.test'],
            [
                'name' => 'Admin',
                'email' => 'admin@inlando.test',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
        
        // Assign admin role via direct DB insertion
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $adminRole->id,
                    'model_type' => User::class,
                    'model_id' => $admin->id
                ]
            );
        }

        // Vendor-User
        $vendor = User::updateOrCreate(
            ['email' => 'vendor@inlando.test'],
            [
                'name' => 'Vendor',
                'email' => 'vendor@inlando.test',
                'password' => Hash::make('vendor123'),
                'is_admin' => false,
                'is_vendor' => true,
            ]
        );
        
        // Assign vendor role via direct DB insertion
        $vendorRole = Role::where('name', 'vendor')->first();
        if ($vendorRole) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $vendorRole->id,
                    'model_type' => User::class,
                    'model_id' => $vendor->id
                ]
            );
        }

        // Regular user/customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@inlando.test'],
            [
                'name' => 'Customer',
                'email' => 'customer@inlando.test',
                'password' => Hash::make('customer123'),
                'is_admin' => false,
                'is_vendor' => false,
            ]
        );
        
        // Assign user role via direct DB insertion
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $userRole->id,
                    'model_type' => User::class,
                    'model_id' => $customer->id
                ]
            );
        }
    }
}
