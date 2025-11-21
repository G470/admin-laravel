<?php

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
// use App\Http\Controllers\Admin\RoleController; // Commented out - controller doesn't exist
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CitySeoController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\BadwordController;
use App\Http\Controllers\Admin\PaymentProviderController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\RentalFieldTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\LocationsController;
use App\Http\Controllers\Admin\OpeningsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ContactDetailsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\VendorEarningsController;
use App\Http\Controllers\Admin\CreditPackageController;
use App\Http\Controllers\Admin\CreditGrantController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\TwoFactorController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//======================================================================
// INLANDO FRONTEND ROUTES
//======================================================================

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

// Test routes for development
Route::get('/test-breadcrumb', function () {
    return view('test-breadcrumb');
})->name('test.breadcrumb');

Route::get('/test-breadcrumb-category', function () {
    return view('test-breadcrumb-category');
})->name('test.breadcrumb.category');

Route::get('/test-breadcrumb-real', function () {
    return view('test-breadcrumb-real');
})->name('test.breadcrumb.real');

Route::get('/test-subcategories', function () {
    return view('test-subcategories');
})->name('test.subcategories');

Route::get('/test-rental-list-subcategories', function () {
    return view('test-rental-list-subcategories');
})->name('test.rental-list.subcategories');

Route::get('/test-rental-list-recursive', function () {
    return view('test-rental-list-recursive');
})->name('test.rental-list.recursive');

Route::get('/test-dynamic-fields-storage', function () {
    return view('test-dynamic-fields-storage');
})->name('test.dynamic.fields.storage');

Route::get('/test-dynamic-fields-pending', function () {
    return view('test-dynamic-fields-pending');
})->name('test.dynamic.fields.pending');

Route::post('/test-dynamic-fields-pending-clear', function () {
    $categoryId = request('category_id', 139); // Default category ID
    \App\Helpers\DynamicRentalFields::clearPendingValues($categoryId);
    return response()->json(['success' => true]);
})->name('test.dynamic.fields.pending.clear');

Route::get('/test-dynamic-fields-integration', function () {
    return view('test-dynamic-fields-integration');
})->name('test.dynamic.fields.integration');

//======================================================================
// AUTHENTICATION ROUTES
//======================================================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Forgot Password Routes
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Reset Password Routes
    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//======================================================================
// BOOKING ROUTES
//======================================================================

// Public booking routes (guest access via token)
Route::get('/booking/{token}', [App\Http\Controllers\BookingController::class, 'showByToken'])
    ->where('token', '[a-zA-Z0-9]{32}')
    ->name('booking.token');

// Authenticated user booking routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])->name('bookings.cancel');
});

//======================================================================
// LOGGED-IN USER ROUTES
//======================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mein-profil', function () {
        return 'User Profile Page';
    })->name('user.profile');
    Route::get('/meine-buchungen', [App\Http\Controllers\BookingController::class, 'index'])->name('user.bookings.index');
    Route::get('/meine-buchungen/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('user.bookings.show');
    Route::get('/meine-buchungen/{booking}/bearbeiten', [App\Http\Controllers\BookingController::class, 'edit'])->name('user.bookings.edit');
    Route::get('/meine-buchungen/{booking}/anfrage', [App\Http\Controllers\BookingController::class, 'request'])->name('user.bookings.request');
    // dashboard routes
    Route::get('/mein-dashboard', function () {
        return 'User Dashboard Page';
    })->name('user.dashboard');
    Route::get('/meine-anfragen', [App\Http\Controllers\BookingController::class, 'index'])->name('user.bookings');
    Route::get('/meine-favoriten', [FavoritesController::class, 'index'])->name('user.favorites');
    Route::get('/einstellungen', function () {
        return 'User Settings Page';
    })->name('user.settings');
});

//======================================================================
// VENDOR ROUTES
//======================================================================

Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('vendor-dashboard');

    // Booking Management Routes
    Route::get('/bookings', [\App\Http\Controllers\Vendor\BookingController::class, 'index'])->name('vendor.bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Vendor\BookingController::class, 'show'])->name('vendor.bookings.show');
    Route::patch('/bookings/{booking}/confirm', [App\Http\Controllers\BookingController::class, 'confirm'])->name('vendor.bookings.confirm');
    // we need a get route for the booking confirmation page
    Route::get('/bookings/{booking}/confirm', [\App\Http\Controllers\BookingController::class, 'confirmPage'])->name('vendor.bookings.confirm.page');
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])->name('vendor.bookings.cancel');
    Route::patch('/bookings/{booking}/accept', [App\Http\Controllers\BookingController::class, 'accept'])->name('vendor.bookings.accept');
    Route::patch('/bookings/{booking}/reject', [App\Http\Controllers\BookingController::class, 'reject'])->name('vendor.bookings.reject');
    Route::patch('/bookings/{booking}/complete', [\App\Http\Controllers\Vendor\BookingController::class, 'complete'])->name('vendor.bookings.complete');
    Route::post('/bookings/{booking}/notes', [\App\Http\Controllers\Vendor\BookingController::class, 'addNotes'])->name('vendor.bookings.notes');

    // Personal Data Routes
    Route::get('/personal-data', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'index'])->name('vendor.personal-data');
    Route::put('/personal-data', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updatePersonalData'])->name('vendor.personal-data.update');
    Route::put('/company-data', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updateCompanyData'])->name('vendor.company-data.update');
    Route::put('/billing-address', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updateBillingAddress'])->name('vendor.billing-address.update');
    Route::put('/email', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updateEmail'])->name('vendor.email.update');
    Route::get('/email/confirm/{token}', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'confirmEmailChange'])->name('vendor.email.confirm');
    Route::get('/email/cancel/{token}', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'cancelEmailChange'])->name('vendor.email.cancel');
    Route::put('/password', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updatePassword'])->name('vendor.password.update');
    Route::put('/avatar', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'updateAvatar'])->name('vendor.avatar.update');
    Route::delete('/avatar', [\App\Http\Controllers\Vendor\PersonalDataController::class, 'deleteAvatar'])->name('vendor.avatar.delete');

    // Rental Routes (Vermietungsobjekte)
    Route::get('/rentals', [\App\Http\Controllers\VendorController::class, 'rentals'])->name('vendor.rentals.index');
    Route::get('/rental/{id?}', [\App\Http\Controllers\VendorController::class, 'rental'])->name('vendor.rental');
    Route::get('/rental/{id}/edit', [\App\Http\Controllers\VendorController::class, 'rental'])->name('vendor-rental-edit');
    Route::get('/rental/{id}/preview', [\App\Http\Controllers\VendorController::class, 'rentalPreview'])->name('vendor-rental-preview');
    Route::post('/rental/save/{id?}', [\App\Http\Controllers\VendorController::class, 'saveRental'])->name('vendor-rental-save');
    Route::post('/rentals/bulk-action', [\App\Http\Controllers\VendorController::class, 'bulkAction'])->name('vendor-rentals-bulk-action');

    // Individual Rental Actions
    Route::post('/rental/{id}/duplicate', [\App\Http\Controllers\VendorController::class, 'duplicateRental'])->name('vendor-rental-duplicate');
    Route::post('/rental/{id}/toggle-status', [\App\Http\Controllers\VendorController::class, 'toggleRentalStatus'])->name('vendor-rental-toggle-status');
    Route::delete('/rental/{id}', [\App\Http\Controllers\VendorController::class, 'deleteRental'])->name('vendor-rental-delete');

    // Location Routes (Standorte)
    Route::get('/standorte', [\App\Http\Controllers\LocationsController::class, 'index'])->name('vendor-locations');
    Route::get('/standort/create', [\App\Http\Controllers\LocationsController::class, 'create'])->name('vendor-location-create');
    Route::get('/standort/{id?}', [\App\Http\Controllers\LocationsController::class, 'location'])->name('vendor-location-edit');
    Route::post('/standort/save/{id?}', [\App\Http\Controllers\LocationsController::class, 'save'])->name('vendor-location-save');
    Route::put('/standort/update/{id?}', [\App\Http\Controllers\LocationsController::class, 'update'])->name('vendor-location-update');
    Route::delete('/standort/{id}', [\App\Http\Controllers\LocationsController::class, 'destroy'])->name('vendor-location-destroy');
    Route::post('/standort/{id}/set-main', [\App\Http\Controllers\LocationsController::class, 'setMain'])->name('vendor-location-set-main');

    // Opening Hours Routes
    Route::get('/oeffnungszeiten', [\App\Http\Controllers\OpeningsController::class, 'indexMain'])->name('vendor-openings-index');
    Route::post('/oeffnungszeiten/defaults', [\App\Http\Controllers\OpeningsController::class, 'updateDefaults'])->name('vendor-openings-update-defaults');
    Route::delete('/oeffnungszeiten/{locationId}/remove', [\App\Http\Controllers\OpeningsController::class, 'removeLocationHours'])->name('vendor-openings-remove');
    Route::get('/oeffnungszeiten/{locationId}', [\App\Http\Controllers\OpeningsController::class, 'opening'])->name('vendor-openings-edit');
    Route::put('/oeffnungszeiten/update/{locationId}', [\App\Http\Controllers\OpeningsController::class, 'update'])->name('vendor-openings-update');

    
    // Notification Options Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\NotificationController::class, 'index'])->name('index');
        Route::post('/default', [\App\Http\Controllers\Vendor\NotificationController::class, 'updateDefault'])->name('update-default');
        Route::get('/location/{location}', [\App\Http\Controllers\Vendor\NotificationController::class, 'getLocationSettings'])->name('location-settings');
        Route::post('/location/{location}', [\App\Http\Controllers\Vendor\NotificationController::class, 'updateLocation'])->name('update-location');
        Route::delete('/location/{location}', [\App\Http\Controllers\Vendor\NotificationController::class, 'resetLocation'])->name('reset-location');
    });

    // Contact Details Routes
    Route::prefix('kontaktdaten')->name('contact-details.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\ContactDetailsController::class, 'index'])->name('index');
        Route::post('/default', [\App\Http\Controllers\Vendor\ContactDetailsController::class, 'updateDefault'])->name('update-default');
        Route::get('/location/{location}', [\App\Http\Controllers\Vendor\ContactDetailsController::class, 'getLocationDetails'])->name('location-details');
        Route::post('/location/{location}', [\App\Http\Controllers\Vendor\ContactDetailsController::class, 'updateLocation'])->name('update-location');
        Route::delete('/location/{location}', [\App\Http\Controllers\Vendor\ContactDetailsController::class, 'resetLocation'])->name('reset-location');
    });

    // Statistics Routes (Statistiken)
    Route::get('/statistiken', [\App\Http\Controllers\VendorController::class, 'statistics'])->name('vendor.statistiken');

    // Communication Routes (Kommunikation)
    Route::get('/nachrichten', [\App\Http\Controllers\VendorController::class, 'messages'])->name('vendor.nachrichten');

    // Finance Routes (Finanzen)
    Route::get('/rechnungen', [\App\Http\Controllers\VendorController::class, 'bills'])->name('vendor.rechnungen');
    Route::get('/guthaben', [\App\Http\Controllers\VendorController::class, 'credits'])->name('vendor.guthaben');

    // Reviews Routes
    Route::get('/bewertungen', [\App\Http\Controllers\Vendor\ReviewsController::class, 'index'])->name('vendor.bewertungen');
    Route::get('/bewertungen/{id}', [\App\Http\Controllers\Vendor\ReviewsController::class, 'show'])->name('vendor.reviews.show');

    // Credit Management Routes
    Route::get('/credits', [\App\Http\Controllers\Vendor\CreditController::class, 'index'])->name('vendor.credits.index');
    Route::post('/credits/purchase/{creditPackage}', [\App\Http\Controllers\Vendor\CreditController::class, 'purchase'])->name('vendor.credits.purchase');
    Route::get('/credits/payment/{vendorCredit}', [\App\Http\Controllers\Vendor\CreditController::class, 'payment'])->name('vendor.credits.payment');
    Route::get('/credits/success/{vendorCredit}', [\App\Http\Controllers\Vendor\CreditController::class, 'paymentSuccess'])->name('vendor.credits.payment.success');
    Route::get('/credits/history', [\App\Http\Controllers\Vendor\CreditController::class, 'history'])->name('vendor.credits.history');

    // Membership & Packages Routes
    Route::get('/membership', [\App\Http\Controllers\Vendor\MembershipController::class, 'index'])->name('vendor.membership.index');
    Route::post('/membership/cancel', [\App\Http\Controllers\Vendor\MembershipController::class, 'cancel'])->name('vendor.membership.cancel');
    Route::post('/membership/change', [\App\Http\Controllers\Vendor\MembershipController::class, 'change'])->name('vendor.membership.change');

    // Rental Push Routes (Artikel-Push)
    Route::prefix('rental-pushes')->name('vendor.rental-pushes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\RentalPushController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Vendor\RentalPushController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Vendor\RentalPushController::class, 'store'])->name('store');
        Route::get('/{rentalPush}', [\App\Http\Controllers\Vendor\RentalPushController::class, 'show'])->name('show');
        Route::get('/{rentalPush}/edit', [\App\Http\Controllers\Vendor\RentalPushController::class, 'edit'])->name('edit');
        Route::put('/{rentalPush}', [\App\Http\Controllers\Vendor\RentalPushController::class, 'update'])->name('update');
        Route::delete('/{rentalPush}', [\App\Http\Controllers\Vendor\RentalPushController::class, 'destroy'])->name('destroy');
        Route::patch('/{rentalPush}/toggle-status', [\App\Http\Controllers\Vendor\RentalPushController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/statistics', [\App\Http\Controllers\Vendor\RentalPushController::class, 'statistics'])->name('statistics');
    });

    // Dynamic fields loading route
    Route::get('/dynamic-fields/{categoryId}', [\App\Http\Controllers\VendorController::class, 'loadDynamicFields'])->name('vendor.dynamic-fields');
});

//======================================================================
// ADMIN ROUTES (Placeholder - controllers need to be created)
//======================================================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // cities SEO routes
    Route::get('/staedte-seo', [CitySeoController::class, 'index'])->name('cities-seo.index');
    Route::get('/staedte-seo/create', [CitySeoController::class, 'create'])->name('cities-seo.create');
    Route::post('/staedte-seo', [CitySeoController::class, 'store'])->name('cities-seo.store');
    Route::get('/staedte-seo/{id}/edit', [CitySeoController::class, 'edit'])->name('cities-seo.edit');
    Route::put('/staedte-seo/{id}', [CitySeoController::class, 'update'])->name('cities-seo.update');
    Route::delete('/staedte-seo/{id}', [CitySeoController::class, 'destroy'])->name('cities-seo.destroy');
    // formular management routes
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{id}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{id}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{id}', [FormController::class, 'destroy'])->name('forms.destroy');
    Route::patch('/forms/{form}/toggle-status', [FormController::class, 'toggleStatus'])->name('forms.toggle-status');
    // email-templates management routes
    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
    Route::post('/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
    Route::get('/email-templates/{id}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{id}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::delete('/email-templates/{id}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
    // badwords management routes
    Route::get('/badwords', [BadwordController::class, 'index'])->name('badwords.index');
    Route::get('/badwords/create', [BadwordController::class, 'create'])->name('badwords.create');
    Route::post('/badwords', [BadwordController::class, 'store'])->name('badwords.store');
    Route::get('/badwords/{id}/edit', [BadwordController::class, 'edit'])->name('badwords.edit');
    Route::put('/badwords/{id}', [BadwordController::class, 'update'])->name('badwords.update');
    Route::delete('/badwords/{id}', [BadwordController::class, 'destroy'])->name('badwords.destroy');
    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    // Rental management routes
    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create', [AdminRentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [AdminRentalController::class, 'store'])->name('rentals.store');
    Route::get('/rentals/{rental}', [AdminRentalController::class, 'show'])->name('rentals.show');
    Route::get('/rentals/{rental}/edit', [AdminRentalController::class, 'edit'])->name('rentals.edit');
    Route::put('/rentals/{rental}', [AdminRentalController::class, 'update'])->name('rentals.update');
    Route::delete('/rentals/{rental}', [AdminRentalController::class, 'destroy'])->name('rentals.destroy');
    Route::patch('/rentals/{rental}/toggle-status', [AdminRentalController::class, 'toggleStatus'])->name('rentals.toggle-status');
    Route::patch('/rentals/{rental}/toggle-featured', [AdminRentalController::class, 'toggleFeatured'])->name('rentals.toggle-featured');

    // Rental Field Template management routes
    Route::get('/rental-field-templates', [RentalFieldTemplateController::class, 'index'])->name('rental-field-templates.index');
    Route::get('/rental-field-templates/create', [RentalFieldTemplateController::class, 'create'])->name('rental-field-templates.create');
    Route::post('/rental-field-templates', [RentalFieldTemplateController::class, 'store'])->name('rental-field-templates.store');
    Route::get('/rental-field-templates/{rentalFieldTemplate}', [RentalFieldTemplateController::class, 'show'])->name('rental-field-templates.show');
    Route::get('/rental-field-templates/{rentalFieldTemplate}/edit', [RentalFieldTemplateController::class, 'edit'])->name('rental-field-templates.edit');
    Route::put('/rental-field-templates/{rentalFieldTemplate}', [RentalFieldTemplateController::class, 'update'])->name('rental-field-templates.update');
    Route::delete('/rental-field-templates/{rentalFieldTemplate}', [RentalFieldTemplateController::class, 'destroy'])->name('rental-field-templates.destroy');
    Route::post('/rental-field-templates/{rentalFieldTemplate}/duplicate', [RentalFieldTemplateController::class, 'duplicate'])->name('rental-field-templates.duplicate');
    Route::patch('/rental-field-templates/{rentalFieldTemplate}/toggle-status', [RentalFieldTemplateController::class, 'toggleStatus'])->name('rental-field-templates.toggle-status');
    Route::get('/rental-field-templates/{rentalFieldTemplate}/export', [RentalFieldTemplateController::class, 'export'])->name('rental-field-templates.export');
    Route::post('/rental-field-templates/import', [RentalFieldTemplateController::class, 'import'])->name('rental-field-templates.import');
    Route::post('/rental-field-templates/reorder', [RentalFieldTemplateController::class, 'reorder'])->name('rental-field-templates.reorder');
    Route::get('/rental-field-templates/{rentalFieldTemplate}/usage-stats', [RentalFieldTemplateController::class, 'getUsageStats'])->name('rental-field-templates.usage-stats');
    Route::get('/rental-field-templates/{template}/fields/{fieldId}', [RentalFieldTemplateController::class, 'getField'])->name('rental-field-templates.fields.get');
    Route::post('/rental-field-templates/{template}/fields', [RentalFieldTemplateController::class, 'storeField'])->name('rental-field-templates.fields.store');
    Route::put('/rental-field-templates/{template}/fields/{fieldId}', [RentalFieldTemplateController::class, 'updateField'])->name('rental-field-templates.fields.update');
    Route::delete('/rental-field-templates/{template}/fields/{fieldId}', [RentalFieldTemplateController::class, 'deleteField'])->name('rental-field-templates.fields.delete');
    // AJAX endpoints
    Route::get('/api/rental-field-templates/for-category', [RentalFieldTemplateController::class, 'getForCategory'])->name('rental-field-templates.for-category');

    // Category management routes
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    // Location management routes
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::get('/locations/{id}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::put('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
    // Opening hours management routes
    Route::get('/openings', [OpeningsController::class, 'index'])->name('openings.index');
    Route::get('/openings/create', [OpeningsController::class, 'create'])->name('openings.create');
    Route::post('/openings', [OpeningsController::class, 'store'])->name('openings.store');
    Route::get('/openings/{id}/edit', [OpeningsController::class, 'edit'])->name('openings.edit');
    Route::put('/openings/{id}', [OpeningsController::class, 'update'])->name('openings.update');
    Route::delete('/openings/{id}', [OpeningsController::class, 'destroy'])->name('openings.destroy');
    // Review management routes
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    // Admin settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/cache-clear', [SettingsController::class, 'clearCache'])->name('settings.cache-clear');
    Route::post('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/maintenance', [SettingsController::class, 'maintenanceMode'])->name('settings.maintenance');
    Route::post('/settings/test-smtp', [SettingsController::class, 'testSMTP'])->name('settings.test-smtp');
    Route::get('/settings/debug-smtp', [SettingsController::class, 'debugSMTP'])->name('settings.debug-smtp');
    // Admin profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin messages routes
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'store'])->name('messages.store');
    Route::put('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('messages.destroy');

    // Country management routes
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create');
    Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
    Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');
    Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
    Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
    Route::patch('/countries/{country}/toggle', [CountryController::class, 'toggle'])->name('countries.toggle');

    // Country data import routes
    Route::get('/countries/{country}/import', [CountryController::class, 'importForm'])->name('countries.import');
    Route::post('/countries/{country}/import/preview', [CountryController::class, 'importPreview'])->name('countries.import.preview');
    Route::post('/countries/{country}/import/execute', [CountryController::class, 'executeImport'])->name('countries.import.execute');
    Route::get('/countries/{country}/import/stats', [CountryController::class, 'importStats'])->name('countries.import.stats');
    Route::post('/countries/{country}/data/clear', [CountryController::class, 'clearData'])->name('countries.data.clear');
    Route::get('/countries/{country}/data/export', [CountryController::class, 'exportData'])->name('countries.data.export');
    Route::get('/countries/{country}/data/view', [CountryController::class, 'viewData'])->name('countries.data.view');
    Route::get('/countries/{country}/data/table', [CountryController::class, 'getDataTable'])->name('countries.data.table');

    // Credit Package management routes
    Route::get('/credit-packages', [CreditPackageController::class, 'index'])->name('credit-packages.index');
    Route::get('/credit-packages/create', [CreditPackageController::class, 'create'])->name('credit-packages.create');
    Route::post('/credit-packages', [CreditPackageController::class, 'store'])->name('credit-packages.store');
    Route::get('/credit-packages/{creditPackage}/edit', [CreditPackageController::class, 'edit'])->name('credit-packages.edit');
    Route::put('/credit-packages/{creditPackage}', [CreditPackageController::class, 'update'])->name('credit-packages.update');
    Route::delete('/credit-packages/{creditPackage}', [CreditPackageController::class, 'destroy'])->name('credit-packages.destroy');
    Route::patch('/credit-packages/{creditPackage}/toggle', [CreditPackageController::class, 'toggle'])->name('credit-packages.toggle');
    Route::post('/credit-packages/{creditPackage}/duplicate', [CreditPackageController::class, 'duplicate'])->name('credit-packages.duplicate');
    Route::get('/credit-analytics', [CreditPackageController::class, 'analytics'])->name('credit-analytics');

    // Admin Credit Grants routes
    Route::get('/credit-grants', [CreditGrantController::class, 'index'])->name('credit-grants.index');
    Route::get('/credit-grants/create', [CreditGrantController::class, 'create'])->name('credit-grants.create');
    Route::post('/credit-grants', [CreditGrantController::class, 'store'])->name('credit-grants.store');
    Route::get('/credit-grants/{creditGrant}', [CreditGrantController::class, 'show'])->name('credit-grants.show');
    Route::get('/credit-grants/{creditGrant}/edit', [CreditGrantController::class, 'edit'])->name('credit-grants.edit');
    Route::put('/credit-grants/{creditGrant}', [CreditGrantController::class, 'update'])->name('credit-grants.update');
    Route::delete('/credit-grants/{creditGrant}', [CreditGrantController::class, 'destroy'])->name('credit-grants.destroy');
    Route::get('/credit-grants/statistics', [CreditGrantController::class, 'statistics'])->name('credit-grants.statistics');
    Route::get('/credit-grants/export', [CreditGrantController::class, 'export'])->name('credit-grants.export');

    // Role Management routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');
    Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions.assign');

    // Permission Management routes
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/statistics', [PermissionController::class, 'statistics'])->name('permissions.statistics');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('/permissions/{permission}/assign-roles', [PermissionController::class, 'assignToRoles'])->name('permissions.assign-roles');

    // Bills management routes
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/bills/{id}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/bills/{id}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/bills/{id}', [BillController::class, 'update'])->name('bills.update');
    Route::delete('/bills/{id}', [BillController::class, 'destroy'])->name('bills.destroy');
    Route::get('/bills/{id}/download', [BillController::class, 'download'])->name('bills.download');

    // Vendor earnings routes
    Route::get('/vendor-earnings', [VendorEarningsController::class, 'index'])->name('vendor-earnings.index');
    Route::get('/vendor-earnings/{id}', [VendorEarningsController::class, 'show'])->name('vendor-earnings.show');

    // Rental field templates routes
    Route::get('/rental-field-templates', [RentalFieldTemplateController::class, 'index'])->name('rental-field-templates.index');
    Route::get('/rental-field-templates/create', [RentalFieldTemplateController::class, 'create'])->name('rental-field-templates.create');
    Route::post('/rental-field-templates', [RentalFieldTemplateController::class, 'store'])->name('rental-field-templates.store');
    Route::get('/rental-field-templates/{rentalFieldTemplate}/edit', [RentalFieldTemplateController::class, 'edit'])->name('rental-field-templates.edit');
    Route::put('/rental-field-templates/{rentalFieldTemplate}', [RentalFieldTemplateController::class, 'update'])->name('rental-field-templates.update');
    Route::delete('/rental-field-templates/{rentalFieldTemplate}', [RentalFieldTemplateController::class, 'destroy'])->name('rental-field-templates.destroy');

    // Subscription plans routes
    Route::get('/abonnementplaene', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::get('/abonnementplaene/create', [SubscriptionPlanController::class, 'create'])->name('subscription-plans.create');
    Route::post('/abonnementplaene', [SubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
    Route::get('/abonnementplaene/{id}/edit', [SubscriptionPlanController::class, 'edit'])->name('subscription-plans.edit');
    Route::put('/abonnementplaene/{id}', [SubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
    Route::delete('/abonnementplaene/{id}', [SubscriptionPlanController::class, 'destroy'])->name('subscription-plans.destroy');

    // Payment Provider API Keys routes
    Route::get('/payment-providers', [App\Http\Controllers\Admin\PaymentProviderController::class, 'index'])->name('payment-providers.index');
    Route::put('/payment-providers', [App\Http\Controllers\Admin\PaymentProviderController::class, 'update'])->name('payment-providers.update');
    Route::post('/payment-providers/test-connection', [App\Http\Controllers\Admin\PaymentProviderController::class, 'testConnection'])->name('payment-providers.test-connection');
    Route::get('/payment-providers/status', [App\Http\Controllers\Admin\PaymentProviderController::class, 'status'])->name('payment-providers.status');
    Route::post('/payment-providers/toggle-environment', [App\Http\Controllers\Admin\PaymentProviderController::class, 'toggleEnvironment'])->name('payment-providers.toggle-environment');
    Route::get('/payment-providers/active-environments', [App\Http\Controllers\Admin\PaymentProviderController::class, 'getActiveEnvironments'])->name('payment-providers.active-environments');

    // Homepage settings routes
    Route::get('/homepage', [HomepageController::class, 'index'])->name('homepage.index');
    Route::put('/homepage', [HomepageController::class, 'update'])->name('homepage.update');
    Route::put('/homepage/categories', [HomepageController::class, 'updateCategoriesPage'])->name('homepage.categories.update');
});

//======================================================================
// GENERAL ROUTES
//======================================================================

// Language switching
Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);
    return redirect()->back();
});
// language.swap route
Route::get('/language/swap', function () {
    $locale = session('locale', config('app.locale'));
    $newLocale = $locale === 'en' ? 'de' : 'en';
    session(['locale' => $newLocale]);
    return redirect()->back();
})->name('language.swap');

//======================================================================
// LOCATION SEO ROUTES (Category + Location combinations)
//======================================================================

// City overview page - lists all cities with rentals
Route::get('/standorte', [\App\Http\Controllers\Inlando\LocationController::class, 'cityOverview'])->name('cities.overview');

// Category + Location SEO pages
Route::get(
    '/mieten/{category1}/{category2?}/{category3?}/{location}',
    [\App\Http\Controllers\Inlando\CategoryLocationController::class, 'show']
)
    ->name('category.location.show')
    ->where([
        'category1' => '[a-zA-Z0-9_-]+',
        'category2' => '[a-zA-Z0-9_-]+',
        'category3' => '[a-zA-Z0-9_-]+',
        'location' => '[a-zA-Z0-9_-]+'
    ]);

// Generic location pages (all categories)
Route::get('/mieten/{location}', [\App\Http\Controllers\Inlando\LocationController::class, 'show'])
    ->name('location.show')
    ->where('location', '[a-zA-Z0-9_-]+');

//======================================================================
// SITEMAP ROUTES
//======================================================================
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-static.xml', [\App\Http\Controllers\SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/sitemap-locations.xml', [\App\Http\Controllers\SitemapController::class, 'locations'])->name('sitemap.locations');
Route::get('/sitemap-categories.xml', [\App\Http\Controllers\SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-category-locations.xml', [\App\Http\Controllers\SitemapController::class, 'categoryLocations'])->name('sitemap.category-locations');
Route::get('/sitemap-rentals.xml', [\App\Http\Controllers\SitemapController::class, 'rentals'])->name('sitemap.rentals');

//======================================================================
// TWO-FACTOR AUTHENTICATION ROUTES
//======================================================================

// Two-Factor Authentication Routes
Route::middleware(['auth'])->group(function () {
    // Generic 2FA routes (role-agnostic)
    Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('two-factor.index');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'recoveryCodes'])->name('two-factor.recovery-codes');
    Route::post('/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-recovery-codes');
    
    // 2FA verification (during login)
    Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify.post');
    
    // Role-specific 2FA routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('admin.two-factor');
    });
    
    Route::prefix('vendor')->middleware(['vendor'])->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('vendor.two-factor');
    });
    
    Route::prefix('user')->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('user.two-factor');
    });
});

// Note: Admin roles and permissions routes are defined in the main admin group above
