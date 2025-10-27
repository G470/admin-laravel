@php
    $configData = Helper::appClasses();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $configData['style'] }}-style"
    data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{url('/')}}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- admin seo integration. If a category is displayed check if the category has a seo title and description. If not, use the category name and description. -->
    @if(isset($category))
        @if($category->meta_title)
            <title>{{ $category->meta_title }}</title>
        @else
            <title>Inlando | {{ $category->name }} mieten</title>
        @endif
        @if($category->meta_description)
            <meta name="description" content="{{ $category->meta_description }}">
        @else
            <meta name="description" content="{{ $category->description }}">
        @endif
    @else
        <title>@yield('title', config('app.name', 'Inlando')) | {{ config('variables.templateName') }}</title>
    @endif
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Template Variables -->
    <script>
        window.templateName = '{{ config('variables.templateName', 'inlando') }}';
    </script>
    <!-- Include Core Styles -->
    @vite([
        'resources/css/app.css',
        'resources/assets/vendor/fonts/fontawesome.scss',
        'resources/assets/vendor/fonts/tabler-icons.scss',
        'resources/assets/vendor/fonts/flag-icons.scss',
    // 'resources/assets/vendor/css/rtl/core.scss',
        'resources/assets/vendor/scss/rtl/theme-default.scss',
        'resources/assets/vendor/scss/demo.scss',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/assets/vendor/libs/node-waves/node-waves.scss',
        'resources/assets/vendor/libs/daterangepicker/daterangepicker.scss',
        'resources/assets/vendor/scss/pages/front-page.scss'
    ])

    @stack('page-styles')
    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    @include('layouts.frontend.sections.navbar.navbar')

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.frontend.sections.footer.footer')

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
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/daterangepicker/daterangepicker.js',
        'resources/assets/js/template-init.js'
    ])

    <!-- App JS - load last -->
    @vite(['resources/assets/js/front-main.js'])

    @stack('page-scripts')
    @yield('scripts')
</body>

</html>