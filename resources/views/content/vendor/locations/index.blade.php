@extends('layouts/contentNavbarLayout')

@section('title', 'Standorte verwalten')

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Standorte
    </h4>

    @livewire('vendor.locations-table')
@endsection