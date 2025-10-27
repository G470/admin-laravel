<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsVendor;
use App\Http\Middleware\RequireTwoFactor;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(LocaleMiddleware::class);

    // Register middleware aliases
    $middleware->alias([
      'admin' => IsAdmin::class,
      'vendor' => IsVendor::class,
      'require-2fa' => RequireTwoFactor::class,
      'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
      'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
      'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
  })
  ->withSchedule(function (Schedule $schedule) {
    // Execute rental pushes every 15 minutes
    $schedule->command('rental-pushes:execute')->everyFifteenMinutes();

    // Clean up expired email change tokens daily
    $schedule->command('email:tokens:cleanup')->daily();
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();
