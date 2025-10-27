@php
$configData = Helper::appClasses();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $configData['style'] }}-style" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Inlando Admin')) | {{ config('variables.templateName') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Include Helpers JS early -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>
        window.templateName = '{{ config('variables.templateName', 'inlando') }}';
    </script>

    <!-- Include Core Styles -->
    @vite([
        'resources/assets/vendor/fonts/fontawesome.scss',
        'resources/assets/vendor/fonts/tabler-icons.scss',
        'resources/assets/vendor/fonts/flag-icons.scss',
    // 'resources/assets/vendor/scss/rtl/core.scss',
        'resources/assets/vendor/scss/rtl/theme-default.scss',
        'resources/assets/vendor/scss/demo.scss',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/assets/vendor/libs/node-waves/node-waves.scss',
        'resources/assets/vendor/scss/pages/admin.scss'
    ])

    @stack('page-styles')
    @yield('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @include('layouts.backend.sections.sidebar.sidebar')

            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.backend.sections.navbar.navbar')

                <!-- Content -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    @include('layouts.backend.sections.footer.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

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
        'resources/assets/js/template-init.js',
        'resources/assets/js/admin-main.js'
    ])

    @stack('page-scripts')
    @yield('scripts')
</body>
</html>
