<?php
/**
 * Frontend routes - User-facing application
 * These routes will only be loaded in the frontend container
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inlando\SearchController;
use App\Http\Controllers\Inlando\FavoritesController;
use App\Http\Controllers\Inlando\CategoryController;
use App\Http\Controllers\Inlando\CategoriesPageController;
use App\Http\Controllers\Inlando\InlandoStartpageController;
use App\Http\Controllers\Inlando\RentalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HealthController;

// Health check route
Route::get('/health', [HealthController::class, 'check']);

// Public frontend routes
Route::get('/', [CategoriesPageController::class, 'index'])->name('home');
Route::get('/kategorien', [CategoriesPageController::class, 'index'])->name('categories.index');
Route::get('/suche', [SearchController::class, 'index'])->name('search');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/rental/{id}', [RentalController::class, 'show'])->name('rentals.show');
Route::get('/rental/{id}/request', [RentalController::class, 'request'])->name('rental.request');
Route::post('/rental/{id}/request', [RentalController::class, 'store'])->name('rental-request-store');

// Vendor profile route
Route::get('/anbieter/{id}', [RentalController::class, 'vendorProfile'])->name('vendor.profile');

// Favorites routes
Route::get('/favoriten', [FavoritesController::class, 'index'])->name('favorites');
Route::get('/guest-favoriten', [FavoritesController::class, 'index'])->name('guestuser.favorites');

// Dynamic category routes - handles any category type from database
Route::get('/kategorien/{type}', [CategoryController::class, 'categoryType'])->name('categories.type');

// Static pages
Route::view('/wie-es-funktioniert', 'pages.how-it-works')->name('how-it-works');
Route::view('/ueber-uns', 'pages.about')->name('about');
Route::view('/jetzt-vermieten', 'pages.rent-out')->name('rent-out');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User-specific routes
    Route::get('/mein-profil', function () {
        return view('content.user.profile');
    })->name('user.profile');
    
    Route::get('/meine-buchungen', function () {
        return view('content.user.bookings');
    })->name('user.bookings');
    
    Route::get('/meine-favoriten', function () {
        return view('content.user.favorites');
    })->name('user.favorites');
    
    Route::get('/einstellungen', function () {
        return view('content.user.settings');
    })->name('user.settings');
});
