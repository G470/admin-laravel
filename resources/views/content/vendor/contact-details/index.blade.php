@extends('layouts/contentNavbarLayout')

@section('title', 'Kontaktdaten für Mieter')

@section('content')
    <div class="container-fluid">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Vendor /</span> Kontaktdaten für Mieter
        </h4>

        @livewire('vendor.contact-details')
    </div>
@endsection