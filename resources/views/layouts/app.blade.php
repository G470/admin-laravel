@php
$configData = Helper::appClasses();
$isFront = true;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $configData['style'] }}-style" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Inlando')) | {{ config('variables.templateName') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Include Core Styles -->
    @section('layoutStyles')
    @vite([
        'resources/assets/vendor/fonts/fontawesome.scss',
        'resources/assets/vendor/fonts/tabler-icons.scss',
        'resources/assets/vendor/fonts/flag-icons.scss',
    // 'resources/assets/vendor/scss/rtl/core.scss',
        'resources/assets/vendor/scss/rtl/theme-default.scss',
        'resources/assets/vendor/scss/demo.scss',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/assets/vendor/libs/node-waves/node-waves.scss',
        'resources/assets/vendor/scss/pages/front-page.scss'
    ])
    @endsection
    @stack('styles')
    @yield('styles')

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>

<body>

    <!-- Navbar -->
    @include('layouts.sections.navbar.navbar-front')

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.sections.footer.footer-front')

    <!-- Core JS -->
    @vite([
        'resources/assets/vendor/libs/jquery/jquery.js',
        'resources/assets/vendor/libs/popper/popper.js',
        'resources/assets/vendor/js/bootstrap.js',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'resources/assets/vendor/libs/node-waves/node-waves.js',
        'resources/assets/vendor/libs/hammer/hammer.js',
        'resources/assets/vendor/js/menu.js',
        'resources/assets/js/front-main.js'
    ])

    <!-- Custom JS -->
    @stack('scripts')
    @yield('scripts')
</body>
</html>
