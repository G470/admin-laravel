@extends('layouts/contentNavbarLayoutFrontend')


@section('title', $vendor->company_name ?: $vendor->name . ' - Anbieter Profil')

@section('content')
<section class="bg-body py-3 border-bottom mb-4">
<div class="container">
    @livewire('search-form')
</div>
</section>
    <!-- Vendor Profile Header -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 col-lg-1 text-center">
                                @if($vendor->company_logo)
                                    <img src="{{ asset('storage/' . $vendor->company_logo) }}"
                                        alt="{{ $vendor->company_name ?: $vendor->name }}" class="rounded-circle"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                @elseif($vendor->profile_image)
                                    <img src="{{ asset('storage/' . $vendor->profile_image) }}" alt="{{ $vendor->name }}"
                                        class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-user ti-xl text-primary"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 col-lg-7">
                                <h2 class="mb-1 text-heading fw-bold">
                                    {{ $vendor->company_name ?: $vendor->name }}
                                </h2>
                                @if($vendor->company_name && $vendor->name)
                                    <p class="text-muted mb-1">{{ $vendor->name }}</p>
                                @endif
                                @if($vendor->salutation && $vendor->first_name && $vendor->last_name)
                                    <p class="text-muted mb-1">{{ $vendor->salutation }} {{ $vendor->first_name }}
                                        {{ $vendor->last_name }}</p>
                                @endif
                                @if($vendor->city)
                                    <p class="text-muted mb-2">
                                        <i class="ti ti-map-pin me-1"></i>
                                        @if($vendor->street){{ $vendor->street }}@if($vendor->house_number)
                                        {{ $vendor->house_number }}@endif, @endif
                                        {{ $vendor->city }}@if($vendor->postal_code), {{ $vendor->postal_code }}@endif
                                        @if($vendor->country), {{ $vendor->country }}@endif
                                    </p>
                                @endif
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-label-success">
                                        <i class="ti ti-check me-1"></i>Verifizierter Anbieter
                                    </span>
                                    @if($vendor->email_verified_at)
                                        <span class="badge bg-label-info">
                                            <i class="ti ti-mail-check me-1"></i>E-Mail bestätigt
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-0 text-primary">{{ $stats['total_rentals'] }}</h4>
                                            <small class="text-muted">Mietobjekte</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted">Kategorien</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-0 text-success">{{ $stats['member_since'] }}</h4>
                                            <small class="text-muted">Mitglied seit</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Description Section -->
    @if($vendor->company_description)
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-building me-2"></i>Über das Unternehmen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($vendor->company_banner)
                                    <div class="col-12 mb-3">
                                        <img src="{{ asset('storage/' . $vendor->company_banner) }}" alt="Company Banner"
                                            class="img-fluid rounded" style="width: 100%; max-height: 200px; object-fit: cover;">
                                    </div>
                                @endif
                                <div class="col-12">
                                    <p class="mb-0">{{ $vendor->company_description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Vendor Contact Information -->
    @if($vendor->street || $vendor->phone || $vendor->mobile || $vendor->email)
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-address-book me-2"></i>Kontaktinformationen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($vendor->street || $vendor->city)
                                    <div class="col-md-4 mb-3">
                                        <h6 class="text-muted mb-1">Adresse</h6>
                                        <p class="mb-0">
                                            @if($vendor->street)
                                                {{ $vendor->street }}@if($vendor->house_number) {{ $vendor->house_number }}@endif<br>
                                                @if($vendor->address_addition){{ $vendor->address_addition }}<br>@endif
                                            @endif
                                            @if($vendor->city){{ $vendor->postal_code }} {{ $vendor->city }}@endif
                                            @if($vendor->country)<br>{{ $vendor->country }}@endif
                                        </p>
                                    </div>
                                @endif

                                <div class="col-md-4 mb-3">
                                    @if($vendor->phone)
                                        <h6 class="text-muted mb-1">Telefon</h6>
                                        <p class="mb-2">
                                            <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">
                                                <i class="ti ti-phone me-1"></i>{{ $vendor->phone }}
                                            </a>
                                        </p>
                                    @endif
                                    @if($vendor->mobile)
                                        <h6 class="text-muted mb-1">Mobil</h6>
                                        <p class="mb-0">
                                            <a href="tel:{{ $vendor->mobile }}" class="text-decoration-none">
                                                <i class="ti ti-device-mobile me-1"></i>{{ $vendor->mobile }}
                                            </a>
                                        </p>
                                    @endif
                                </div>

                                @if($vendor->email)
                                    <div class="col-md-4 mb-3">
                                        <h6 class="text-muted mb-1">E-Mail</h6>
                                        <p class="mb-0">
                                            <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">
                                                <i class="ti ti-mail me-1"></i>{{ $vendor->email }}
                                            </a>
                                        </p>
                                        @if($vendor->company_logo)
                                            <div class="mt-3">
                                                <img src="{{ asset('storage/' . $vendor->company_logo) }}" alt="Company Logo"
                                                    class="img-thumbnail" style="max-height: 80px; max-width: 120px;">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Vendor Rentals -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Kategorien</h5>
                    </div>
                    <!-- show vendor categories -->
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-package me-2"></i>Verfügbare Mietobjekte ({{ $stats['total_rentals'] }})
                        </h5>
                        @if($rentals->hasPages())
                            <small class="text-muted">
                                Seite {{ $rentals->currentPage() }} von {{ $rentals->lastPage() }}
                            </small>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($rentals->count() > 0)
                            <div class="row">
                                @foreach($rentals as $rental)
                                    <div class="col-12 col-md-6 col-lg-6 mb-4">
                                        @include('components.rental-card', ['rental' => $rental])
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($rentals->hasPages())
                                <div class="row">
                                    <div class="col-12">
                                        <nav aria-label="Rental pagination">
                                            {{ $rentals->links() }}
                                        </nav>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-package-off ti-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Keine Mietobjekte verfügbar</h5>
                                <p class="text-muted">Dieser Anbieter hat derzeit keine aktiven Mietobjekte.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Search Button -->
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('search') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Suche
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize favorite buttons
            $('.favorite-btn').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $btn = $(this);
                const rentalId = $btn.data('rental-id');
                const $icon = $btn.find('i');

                // Toggle heart icon
                if ($icon.hasClass('ti-heart')) {
                    $icon.removeClass('ti-heart').addClass('ti-heart-filled');
                    $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                } else {
                    $icon.removeClass('ti-heart-filled').addClass('ti-heart');
                    $btn.removeClass('btn-danger').addClass('btn-outline-danger');
                }

                // Here you would typically make an AJAX call to toggle the favorite
                console.log('Toggle favorite for rental:', rentalId);
            });
        });
    </script>
@endpush