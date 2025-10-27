<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/debug-menu/{user_id}', function ($userId) {
    $user = User::find($userId);
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Manually authenticate the user for this test
    auth()->login($user);
    
    // Get menu data (this will trigger the filtering)
    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $verticalMenuData = json_decode($verticalMenuJson);
    
    // Get user roles
    $userRoles = $user->roles->pluck('name')->toArray();
    
    // Add fallback roles based on user attributes
    if ($user->is_admin) {
        $userRoles[] = 'admin';
    }
    if ($user->is_vendor) {
        $userRoles[] = 'vendor';
    }
    if (!$user->is_admin && !$user->is_vendor) {
        $userRoles[] = 'user';
    }
    
    // Remove duplicates
    $userRoles = array_unique($userRoles);
    
    // Get the filtered menu from the service provider
    $menuServiceProvider = new \App\Providers\MenuServiceProvider(app());
    $reflection = new \ReflectionClass($menuServiceProvider);
    $method = $reflection->getMethod('filterMenuByRoles');
    $method->setAccessible(true);
    
    $filteredMenuData = $method->invoke($menuServiceProvider, $verticalMenuData);
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'is_admin' => $user->is_admin,
            'is_vendor' => $user->is_vendor,
            'roles' => $userRoles
        ],
        'original_menu_count' => count($verticalMenuData->menu),
        'filtered_menu_count' => count($filteredMenuData->menu),
        'filtered_menu' => $filteredMenuData->menu
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.menu');
