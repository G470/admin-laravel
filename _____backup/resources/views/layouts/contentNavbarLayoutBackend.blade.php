@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.backend.app')

@php
  /* Display elements */
  $contentNavbar = ($contentNavbar ?? true);
  $container = ($container ?? 'container-xxl');

  /* Content classes */
  $containerNav = ($containerNav ?? 'container-xxl');
@endphp

@section('styles')
  @yield('vendor-style')
  @yield('page-style')
@endsection

@section('scripts')
  @yield('vendor-script')
  @yield('page-script')
@endsection

@section('content')
  <!-- Content wrapper -->
  <div class="content-wrapper">

    <!-- Content -->
    <div class="{{$container}} flex-grow-1 container-p-y">
      @yield('content')
    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
  </div>
  <!--/ Content wrapper -->
@endsection
