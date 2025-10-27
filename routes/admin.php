<?php
/**
 * Admin-specific routes
 * These routes will only be loaded in the admin container
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CitySeoController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\BadwordController;
use App\Http\Controllers\Admin\PaymentProviderController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\RentalFieldTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\VendorEarningsController;
use App\Http\Controllers\Admin\CreditPackageController;
use App\Http\Controllers\Admin\CreditGrantController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Health check route
Route::get('/health', [HealthController::class, 'adminCheck']);

// Admin authentication routes
Route::get('/login', function () {
    return view('auth.admin-login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Protected admin routes
Route::middleware(['auth', 'admin.auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Vendor Management
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
    Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::patch('/vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
    
    // Rental Management
    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [AdminRentalController::class, 'show'])->name('rentals.show');
    Route::patch('/rentals/{rental}/approve', [AdminRentalController::class, 'approve'])->name('rentals.approve');
    Route::patch('/rentals/{rental}/reject', [AdminRentalController::class, 'reject'])->name('rentals.reject');
    Route::delete('/rentals/{rental}', [AdminRentalController::class, 'destroy'])->name('rentals.destroy');
    
    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    
    // City SEO Management
    Route::get('/cities-seo', [CitySeoController::class, 'index'])->name('cities-seo.index');
    Route::get('/cities-seo/create', [CitySeoController::class, 'create'])->name('cities-seo.create');
    Route::post('/cities-seo', [CitySeoController::class, 'store'])->name('cities-seo.store');
    Route::get('/cities-seo/{citySeo}/edit', [CitySeoController::class, 'edit'])->name('cities-seo.edit');
    Route::put('/cities-seo/{citySeo}', [CitySeoController::class, 'update'])->name('cities-seo.update');
    Route::delete('/cities-seo/{citySeo}', [CitySeoController::class, 'destroy'])->name('cities-seo.destroy');
    
    // Form Management
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
    
    // Email Template Management
    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
    Route::post('/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
    Route::get('/email-templates/{id}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{id}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::delete('/email-templates/{id}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Credit Package Management
    Route::get('/credit-packages', [CreditPackageController::class, 'index'])->name('credit-packages.index');
    Route::get('/credit-packages/create', [CreditPackageController::class, 'create'])->name('credit-packages.create');
    Route::post('/credit-packages', [CreditPackageController::class, 'store'])->name('credit-packages.store');
    Route::get('/credit-packages/{creditPackage}/edit', [CreditPackageController::class, 'edit'])->name('credit-packages.edit');
    Route::put('/credit-packages/{creditPackage}', [CreditPackageController::class, 'update'])->name('credit-packages.update');
    Route::delete('/credit-packages/{creditPackage}', [CreditPackageController::class, 'destroy'])->name('credit-packages.destroy');
    
    // Credit Grant Management
    Route::get('/credit-grants', [CreditGrantController::class, 'index'])->name('credit-grants.index');
    Route::get('/credit-grants/create', [CreditGrantController::class, 'create'])->name('credit-grants.create');
    Route::post('/credit-grants', [CreditGrantController::class, 'store'])->name('credit-grants.store');
    Route::delete('/credit-grants/{creditGrant}', [CreditGrantController::class, 'destroy'])->name('credit-grants.destroy');
    
    // Bills Management
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{id}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/bills/{id}/download', [BillController::class, 'download'])->name('bills.download');
    Route::delete('/bills/{id}', [BillController::class, 'destroy'])->name('bills.destroy');
    
    // Vendor Earnings
    Route::get('/vendor-earnings', [VendorEarningsController::class, 'index'])->name('vendor-earnings.index');
    Route::get('/vendor-earnings/{id}', [VendorEarningsController::class, 'show'])->name('vendor-earnings.show');
    
    // Homepage Management
    Route::get('/homepage', [HomepageController::class, 'index'])->name('homepage.index');
    Route::post('/homepage', [HomepageController::class, 'update'])->name('homepage.update');
    
    // Reviews Management
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Roles & Permissions
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
});
