<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ContainerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register container-specific configurations
        $containerType = env('CONTAINER_TYPE', 'frontend');
        
        match ($containerType) {
            'admin' => $this->registerAdminServices(),
            'frontend' => $this->registerFrontendServices(),
            default => $this->registerDefaultServices(),
        };
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadContainerSpecificRoutes();
        $this->configureContainerMiddleware();
    }

    /**
     * Register admin-specific services
     */
    private function registerAdminServices(): void
    {
        // Admin-specific service bindings
        $this->app->singleton('admin.dashboard', function ($app) {
            return new \App\Services\Admin\DashboardService();
        });

        // Override default views path for admin
        $this->app['view']->addLocation(resource_path('views/admin'));
    }

    /**
     * Register frontend-specific services
     */
    private function registerFrontendServices(): void
    {
        // Frontend-specific service bindings
        $this->app->singleton('frontend.search', function ($app) {
            return new \App\Services\Frontend\SearchService();
        });

        // Frontend-specific configurations
        config(['session.cookie' => 'inlando_frontend_session']);
    }

    /**
     * Register default services
     */
    private function registerDefaultServices(): void
    {
        // Default configuration for local development
    }

    /**
     * Load container-specific routes
     */
    private function loadContainerSpecificRoutes(): void
    {
        $containerType = env('CONTAINER_TYPE', 'frontend');

        if ($containerType === 'admin') {
            // Load only admin routes
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(function () {
                    require base_path('routes/admin.php');
                });
        } else {
            // Load frontend and vendor routes
            Route::middleware('web')
                ->group(function () {
                    require base_path('routes/frontend.php');
                    require base_path('routes/vendor.php');
                });
        }
    }

    /**
     * Configure container-specific middleware
     */
    private function configureContainerMiddleware(): void
    {
        $containerType = env('CONTAINER_TYPE', 'frontend');

        // Add container separation middleware to all routes
        $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\ContainerSeparation::class);

        if ($containerType === 'admin') {
            // Admin-specific middleware configuration
            $this->app['router']->aliasMiddleware('admin.auth', \App\Http\Middleware\AdminAuthentication::class);
        }
    }
}
