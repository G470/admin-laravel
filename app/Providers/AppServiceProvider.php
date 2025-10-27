<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use App\Events\BookingStatusChanged;
use App\Listeners\SendBookingStatusNotification;
use App\Models\Rental;
use App\Models\Location;
use App\Observers\RentalObserver;
use App\Observers\LocationObserver;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
      if ($src !== null) {
        return [
          'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' :
            (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
        ];
      }
      return [];
    });

    // Make Str class globally available in Blade views
    Blade::directive('str', function ($expression) {
        return "<?php echo \Illuminate\Support\Str::$expression; ?>";
    });

    // Alternative: Register Str as a global view variable
    view()->share('Str', \Illuminate\Support\Str::class);

    // Components are auto-discovered in App\Livewire namespace
    // Only explicit registrations needed for specific naming or legacy components

    // Register Livewire components explicitly

    Livewire::component('search-form', \App\Livewire\SearchForm::class);
    Livewire::component('booking-management', \App\Livewire\BookingManagement::class);
    Livewire::component('dynamic-rental-fields-form', \App\Livewire\DynamicRentalFieldsForm::class);
    Livewire::component('dynamic-rental-fields-form', \App\Livewire\Admin\Countries::class);




    Livewire::component('vendor.rentals.categories', \App\Livewire\Vendor\Rentals\Categories::class);
    Livewire::component('vendor.rentals.locations', \App\Livewire\Vendor\Rentals\Locations::class);
    Livewire::component('vendor.rentals.price-management', \App\Livewire\Vendor\Rentals\PriceManagement::class);
    Livewire::component('vendor.rental-image-library', \App\Livewire\Vendor\RentalImageLibrary::class);
    Livewire::component('vendor.rentals-table', \App\Livewire\Vendor\RentalsTable::class);
    Livewire::component('vendor.reviews.reviews-list', \App\Livewire\Vendor\Reviews\ReviewsList::class);

    Livewire::component('admin.subscription-plans', \App\Livewire\Admin\SubscriptionPlans::class);
    Livewire::component('admin.rental-table', \App\Livewire\Admin\RentalTable::class);
    Livewire::component('admin.rental-field-template-manager', \App\Livewire\Admin\RentalFieldTemplateManager::class);
    Livewire::component('admin.reviews', \App\Livewire\Admin\Reviews::class);
    Livewire::component('admin.cities-seo', \App\Livewire\Admin\CitiesSeo::class);
    Livewire::component('admin.countries', \App\Livewire\Admin\Countries::class);
    Livewire::component('admin.locations', \App\Livewire\Admin\Locations::class);
    Livewire::component('admin.categories', \App\Livewire\Admin\Categories::class);
    Livewire::component('frontend.rental-field-display', \App\Livewire\Frontend\RentalFieldDisplay::class);
    Livewire::component('frontend.breadcrumb', \App\Livewire\Frontend\Breadcrumb::class);
    Livewire::component('frontend.rental-gallery', \App\Livewire\Frontend\RentalGallery::class);

    // Register event listeners
    Event::listen(
      BookingStatusChanged::class,
      SendBookingStatusNotification::class,
    );

    // Register observers
    Rental::observe(RentalObserver::class);
    Location::observe(LocationObserver::class);
  }
}
