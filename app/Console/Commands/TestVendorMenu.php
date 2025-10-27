<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestVendorMenu extends Command
{
    protected $signature = 'test:vendor-menu';
    protected $description = 'Test menu filtering for vendor user';

    public function handle()
    {
        // Find vendor user
        $vendor = User::where('is_vendor', true)->first();
        
        if (!$vendor) {
            $this->error('No vendor user found');
            return;
        }
        
        // Login as vendor
        Auth::login($vendor);
        
        $this->info("Testing menu for vendor: {$vendor->name} (ID: {$vendor->id})");
        $this->info("is_admin: " . ($vendor->is_admin ? 'true' : 'false'));
        $this->info("is_vendor: " . ($vendor->is_vendor ? 'true' : 'false'));
        $this->info("Spatie roles: " . $vendor->roles->pluck('name')->implode(', '));
        
        // Get user roles using the same logic as MenuServiceProvider
        $userRoles = $vendor->roles->pluck('name')->toArray();
        
        if ($vendor->is_admin) {
            $userRoles[] = 'admin';
        }
        if ($vendor->is_vendor) {
            $userRoles[] = 'vendor';
        }
        if (!$vendor->is_admin && !$vendor->is_vendor) {
            $userRoles[] = 'user';
        }
        
        $userRoles = array_unique($userRoles);
        $this->info("Final detected roles: " . implode(', ', $userRoles));
        
        // Load and filter menu
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $verticalMenuData = json_decode($verticalMenuJson);
        
        $this->info("\nMenu filtering results:");
        $this->info("------------------------");
        
        foreach ($verticalMenuData->menu as $item) {
            if (isset($item->menuHeader)) {
                $this->info("HEADER: {$item->menuHeader}");
                continue;
            }
            
            if (isset($item->roles)) {
                $hasAccess = false;
                foreach ($item->roles as $requiredRole) {
                    if (in_array($requiredRole, $userRoles)) {
                        $hasAccess = true;
                        break;
                    }
                }
                
                if (!$hasAccess) {
                    $this->error("FILTERED OUT: {$item->name} (requires: " . implode(', ', $item->roles) . ")");
                    continue;
                }
            }
            
            $this->info("SHOWN: {$item->name}");
        }
        
        // Now test the actual MenuServiceProvider
        $this->info("\nTesting MenuServiceProvider directly...");
        
        $provider = new \App\Providers\MenuServiceProvider(app());
        $provider->boot();
        
        $menuData = view()->getShared()['menuData'] ?? null;
        if ($menuData && isset($menuData[0]->menu)) {
            $this->info("Menu items from MenuServiceProvider: " . count($menuData[0]->menu));
            foreach ($menuData[0]->menu as $item) {
                if (isset($item->menuHeader)) {
                    $this->info("HEADER: {$item->menuHeader}");
                } else {
                    $this->info("ITEM: {$item->name}");
                }
            }
        } else {
            $this->error("No menu data found from MenuServiceProvider");
        }
    }
}
