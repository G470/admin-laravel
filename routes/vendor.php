<?php
/**
 * Vendor routes - Vendor dashboard and functionality
 * These routes will be loaded in the frontend container alongside user routes
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\OpeningsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\BookingController as VendorBookingController;
use App\Http\Controllers\Vendor\PersonalDataController;
use App\Http\Controllers\Vendor\ReviewsController;
use App\Http\Controllers\Vendor\CreditController;
use App\Http\Controllers\Vendor\MembershipController;
use App\Http\Controllers\Vendor\RentalPushController;

// Vendor routes - require authentication and vendor role
Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('vendor-dashboard');

    // Rental Management
    Route::get('/rentals', [VendorController::class, 'rentals'])->name('vendor-rentals');
    Route::get('/rental', [VendorController::class, 'rental'])->name('vendor-rental');
    Route::get('/rental/{id}/edit', [VendorController::class, 'editRental'])->name('vendor-rental-edit');
    Route::post('/rental/save/{id?}', [VendorController::class, 'saveRental'])->name('vendor-rental-save');
    Route::post('/rentals/bulk-action', [VendorController::class, 'bulkAction'])->name('vendor-rentals-bulk-action');
    
    // Individual Rental Actions
    Route::post('/rental/{id}/duplicate', [VendorController::class, 'duplicateRental'])->name('vendor-rental-duplicate');
    Route::post('/rental/{id}/toggle-status', [VendorController::class, 'toggleRentalStatus'])->name('vendor-rental-toggle-status');
    Route::delete('/rental/{id}', [VendorController::class, 'deleteRental'])->name('vendor-rental-delete');

    // Booking Management
    Route::get('/bookings', [VendorBookingController::class, 'index'])->name('vendor.bookings.index');
    Route::get('/bookings/{booking}', [VendorBookingController::class, 'show'])->name('vendor.bookings.show');
    Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('vendor.bookings.confirm');
    Route::get('/bookings/{booking}/confirm', [BookingController::class, 'confirmPage'])->name('vendor.bookings.confirm.page');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('vendor.bookings.cancel');
    Route::patch('/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('vendor.bookings.accept');
    Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('vendor.bookings.reject');
    Route::patch('/bookings/{booking}/complete', [VendorBookingController::class, 'complete'])->name('vendor.bookings.complete');
    Route::post('/bookings/{booking}/notes', [VendorBookingController::class, 'addNotes'])->name('vendor.bookings.notes');

    // Personal Data Management
    Route::get('/personal-data', [PersonalDataController::class, 'index'])->name('vendor.personal-data');
    Route::put('/personal-data', [PersonalDataController::class, 'updatePersonalData'])->name('vendor.personal-data.update');
    Route::put('/company-data', [PersonalDataController::class, 'updateCompanyData'])->name('vendor.company-data.update');
    Route::put('/billing-address', [PersonalDataController::class, 'updateBillingAddress'])->name('vendor.billing-address.update');
    Route::put('/email', [PersonalDataController::class, 'updateEmail'])->name('vendor.email.update');
    Route::get('/email/confirm/{token}', [PersonalDataController::class, 'confirmEmailChange'])->name('vendor.email.confirm');
    Route::get('/email/cancel/{token}', [PersonalDataController::class, 'cancelEmailChange'])->name('vendor.email.cancel');
    Route::put('/password', [PersonalDataController::class, 'updatePassword'])->name('vendor.password.update');
    Route::put('/avatar', [PersonalDataController::class, 'updateAvatar'])->name('vendor.avatar.update');

    // Location Management
    Route::get('/standorte', [LocationsController::class, 'index'])->name('vendor-locations');
    Route::get('/standort/create', [LocationsController::class, 'create'])->name('vendor-location-create');
    Route::get('/standort/{id?}', [LocationsController::class, 'location'])->name('vendor-location-edit');
    Route::post('/standort/save/{id?}', [LocationsController::class, 'save'])->name('vendor-location-save');
    Route::put('/standort/update/{id?}', [LocationsController::class, 'update'])->name('vendor-location-update');
    Route::delete('/standort/{id}', [LocationsController::class, 'destroy'])->name('vendor-location-destroy');
    Route::post('/standort/{id}/set-main', [LocationsController::class, 'setMain'])->name('vendor-location-set-main');

    // Opening Hours Management
    Route::get('/oeffnungszeiten', [OpeningsController::class, 'indexMain'])->name('vendor-openings-index');
    Route::post('/oeffnungszeiten/defaults', [OpeningsController::class, 'updateDefaults'])->name('vendor-openings-update-defaults');
    Route::get('/oeffnungszeiten/{locationId}', [OpeningsController::class, 'locationOpenings'])->name('vendor-openings-location');
    Route::post('/oeffnungszeiten/{locationId}/save', [OpeningsController::class, 'saveLocationOpenings'])->name('vendor-openings-location-save');
    Route::post('/oeffnungszeiten/{locationId}/use-defaults', [OpeningsController::class, 'useDefaults'])->name('vendor-openings-use-defaults');

    // Statistics
    Route::get('/statistiken', [VendorController::class, 'statistics'])->name('vendor.statistiken');

    // Communication
    Route::get('/nachrichten', [VendorController::class, 'messages'])->name('vendor.nachrichten');

    // Finance
    Route::get('/rechnungen', [VendorController::class, 'bills'])->name('vendor.rechnungen');
    Route::get('/guthaben', [VendorController::class, 'credits'])->name('vendor.guthaben');

    // Reviews
    Route::get('/bewertungen', [ReviewsController::class, 'index'])->name('vendor.bewertungen');
    Route::get('/bewertungen/{id}', [ReviewsController::class, 'show'])->name('vendor.reviews.show');

    // Credit Management
    Route::get('/credits', [CreditController::class, 'index'])->name('vendor.credits.index');
    Route::post('/credits/purchase/{creditPackage}', [CreditController::class, 'purchase'])->name('vendor.credits.purchase');
    Route::get('/credits/payment/{vendorCredit}', [CreditController::class, 'payment'])->name('vendor.credits.payment');
    Route::get('/credits/success/{vendorCredit}', [CreditController::class, 'paymentSuccess'])->name('vendor.credits.payment.success');
    Route::get('/credits/history', [CreditController::class, 'history'])->name('vendor.credits.history');

    // Membership & Packages
    Route::get('/membership', [MembershipController::class, 'index'])->name('vendor.membership.index');
    Route::post('/membership/cancel', [MembershipController::class, 'cancel'])->name('vendor.membership.cancel');
    Route::post('/membership/change', [MembershipController::class, 'change'])->name('vendor.membership.change');

    // Rental Push Routes
    Route::prefix('rental-pushes')->name('vendor.rental-pushes.')->group(function () {
        Route::get('/', [RentalPushController::class, 'index'])->name('index');
        Route::get('/create', [RentalPushController::class, 'create'])->name('create');
        Route::post('/', [RentalPushController::class, 'store'])->name('store');
        Route::get('/{rentalPush}', [RentalPushController::class, 'show'])->name('show');
        Route::get('/{rentalPush}/edit', [RentalPushController::class, 'edit'])->name('edit');
        Route::put('/{rentalPush}', [RentalPushController::class, 'update'])->name('update');
        Route::delete('/{rentalPush}', [RentalPushController::class, 'destroy'])->name('destroy');
        Route::patch('/{rentalPush}/toggle-status', [RentalPushController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/statistics', [RentalPushController::class, 'statistics'])->name('statistics');
    });

    // Dynamic fields loading route
    Route::get('/dynamic-fields/{categoryId}', [VendorController::class, 'loadDynamicFields'])->name('vendor.dynamic-fields');
});
