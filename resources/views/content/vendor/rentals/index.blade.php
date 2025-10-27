@extends('layouts/contentNavbarLayout')

@section('title', 'Vermietungsobjekte')

@section('vendor-style')
    @livewireStyles
@endsection

@section('vendor-script')
    @livewireScripts
@endsection

@section('page-script')
    {{-- No custom JS needed—Livewire handles filtering and bulk actions --}}
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Vermietungsobjekte
    </h4>

    {{-- Livewire component handles table rendering, filtering, pagination, and bulk actions --}}
    @livewire('vendor.rentals-table')
@endsection