@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Meine Favoriten')

@section('content')
<section class="section-py">
    <div class="container">
        <h1 class="mb-4 fw-semibold">Meine Favoriten</h1>
        <div id="favorite-rentals-container" class="row g-4">
            {{-- Favorite rentals will be loaded here by JavaScript --}}
        </div>
        <div id="no-favorites" class="text-center py-5" style="display: none;">
            <i class="ti ti-heart-broken ti-xl text-muted mb-3"></i>
            <h5 class="text-muted">Noch keine Favoriten</h5>
            <p class="text-muted">Du hast noch keine Artikel als Favoriten markiert. Klicke auf das Herz-Symbol bei einem Artikel, um ihn hier zu speichern.</p>
        </div>
    </div>
</section>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const container = document.getElementById('favorite-rentals-container');
    const noFavoritesMessage = document.getElementById('no-favorites');

    if (favorites.length > 0) {
        // Fetch rental data from the server
        fetch('{{ route("api.rentals.favorites") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: favorites })
        })
        .then(response => response.json())
        .then(rentals => {
            if (rentals.length > 0) {
                rentals.forEach(rental => {
                    const col = document.createElement('div');
                    col.className = 'col-12';
                    col.innerHTML = `
                        <div class="card card-hover h-100 shadow-sm border-0" style="flex-direction: row;">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="width: 250px; height: auto;">
                                <i class="ti ti-package ti-lg text-muted"></i>
                            </div>
                            <div class="card-body" style="flex: 1;">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title text-heading fw-semibold">${rental.title.substring(0, 40)}</h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-primary me-2">${rental.price_range_day}€/Tag</span>
                                        <button class="btn btn-icon btn-danger btn-sm favorite-btn active" data-rental-id="${rental.id}">
                                            <i class="ti ti-heart"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="card-text small text-body">
                                    <i class="ti ti-map-pin"></i> 
                                    ${rental.location ? `${rental.location.city}, ${rental.location.postcode}` : 'Standort nicht verfügbar'}
                                </p>
                                <div class="mb-2">
                                    <span class="small text-muted">${rental.category ? rental.category.name : 'Kategorie'}</span>
                                </div>
                                <a href="/rental/${rental.id}" class="btn btn-outline-primary w-100 mt-2 waves-effect">Details ansehen</a>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });
            } else {
                noFavoritesMessage.style.display = 'block';
            }
        });
    } else {
        noFavoritesMessage.style.display = 'block';
    }
});
</script>
@endsection
