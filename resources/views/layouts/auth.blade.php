@php
$configData = Helper::appClasses();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      class="layout-wide customizer-hide" 
      dir="ltr" 
      data-skin="default" 
      data-bs-theme="light" 
      data-assets-path="{{ asset('/assets') . '/' }}" 
      data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Login') | {{ config('variables.templateName', 'Inlando') }}</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Include Helpers JS early -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>
        window.templateName = '{{ config('variables.templateName', 'inlando') }}';
    </script>

    <!-- Core CSS -->
    @vite([
        'resources/assets/vendor/fonts/fontawesome.scss',
        'resources/assets/vendor/fonts/tabler-icons.scss',
        'resources/assets/vendor/fonts/flag-icons.scss',
        'resources/assets/vendor/scss/core.scss',
        'resources/assets/vendor/scss/demo.scss',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/assets/vendor/libs/node-waves/node-waves.scss',
        'resources/assets/vendor/scss/pages/page-auth.scss',
    ])
    
    <!-- Form Validation CSS (using compiled CSS since it's a vendor library) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

    @stack('page-styles')
    @yield('styles')
</head>

<body>
    @yield('content')

    <!-- Core JS -->
    @vite([
        'resources/assets/vendor/libs/jquery/jquery.js',
        'resources/assets/vendor/libs/popper/popper.js',
        'resources/assets/vendor/js/bootstrap.js',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'resources/assets/vendor/libs/node-waves/node-waves.js',
        'resources/assets/vendor/libs/hammer/hammer.js',
        'resources/assets/vendor/js/helpers.js',
        'resources/assets/vendor/js/menu.js',
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js',
        'resources/assets/js/pages-auth.js',
    ])

    @stack('page-scripts')
    @yield('scripts')
    
    <script>
        // Initialize password toggle and ensure form works
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize password toggle
            if (typeof window.Helpers !== 'undefined' && window.Helpers.initPasswordToggle) {
                window.Helpers.initPasswordToggle();
            }
            
            // Fallback: Ensure form can submit even if FormValidation fails
            const form = document.querySelector('#formAuthentication');
            if (form) {
                // Add a fallback submit handler if FormValidation isn't available
                if (typeof FormValidation === 'undefined') {
                    console.warn('FormValidation library not loaded - using native HTML5 validation');
                }
                
                // Ensure form submits on button click even if validation fails
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.addEventListener('click', function(e) {
                        // Let native validation and FormValidation handle it first
                        // This is just a safety net
                    });
                }
            }
        });
    </script>
</body>
</html>

