<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Inlando - Vermietungsplattform">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Inlando</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendors/css/icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">

    <!-- Vendor CSS -->
    @yield('vendor-style')

    <!-- Page CSS -->
    @yield('page-style')

    <!-- Helpers -->
    <script src="{{ asset('vendors/js/helpers.js') }}"></script>
    <script src="{{ asset('js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.partials.menu')

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.partials.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.partials.footer')

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('vendors/js/core.js') }}"></script>
    <script src="{{ asset('js/core.js') }}"></script>

    <!-- Vendor JS -->
    @yield('vendor-script')

    <!-- Page JS -->
    @yield('page-script')

    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>