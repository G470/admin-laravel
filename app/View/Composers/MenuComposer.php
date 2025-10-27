<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $menuFile = $this->determineMenuFile();
        
        $verticalMenuJson = file_get_contents(base_path($menuFile));
        $verticalMenuData = json_decode($verticalMenuJson);
        
        // Load horizontal menu (fallback to original for now)
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
    }

    /**
     * Determine which menu file to use based on user role
     */
    private function determineMenuFile(): string
    {
        if (!Auth::check()) {
            return 'resources/menu/roles/guest.json';
        }

        $user = Auth::user();
        
        // Admin has highest priority
        if ($user->is_admin) {
            return 'resources/menu/roles/admin.json';
        }
        
        // Vendor has second priority
        if ($user->is_vendor) {
            return 'resources/menu/roles/vendor.json';
        }
        
        // Default to regular user menu
        return 'resources/menu/roles/user.json';
    }
}
