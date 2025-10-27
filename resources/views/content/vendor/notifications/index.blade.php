@extends('layouts/contentNavbarLayout')

@section('title', 'Benachrichtigungsoptionen')

@section('content')
    <div class="container-fluid">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Vendor /</span> Benachrichtigungsoptionen
        </h4>

        @livewire('vendor.notification-options')
    </div>
@endsection