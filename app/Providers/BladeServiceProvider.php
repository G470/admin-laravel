<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class BladeServiceProvider extends ServiceProvider
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
        // Make custom string utilities available globally in Blade
        Blade::directive('strLimit', function ($expression) {
            return "<?php echo Str::limit($expression); ?>";
        });
        
        Blade::directive('strSlug', function ($expression) {
            return "<?php echo Str::slug($expression); ?>";
        });
        
        // Add more custom directives as needed
        Blade::directive('formatPrice', function ($expression) {
            return "<?php echo number_format($expression, 2) . ' €'; ?>";
        });
        
        // Custom directive for truncating text with specific logic
        Blade::directive('truncateText', function ($expression) {
            return "<?php echo Str::limit(strip_tags($expression), 150, '...'); ?>";
        });
    }
}
