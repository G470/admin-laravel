@props(['rental'])

<div class="card card-hover h-100 shadow-sm border-0 rental-card" style="flex-direction: row;">
    <div class="rental-card-image bg-light d-flex align-items-center justify-content-center"
        style="width: 250px; height: 200px; overflow: hidden; position: relative;">
        @php
            // Get primary image from rental-image-library (rental_images table)
            $primaryImage = $rental->images()->orderBy('order')->first();
            
            // Fallback to legacy images array if no library images exist
            if (!$primaryImage && isset($rental->images) && is_array($rental->images) && count($rental->images) > 0) {
                $legacyImageUrl = $rental->images[0];
            }
        @endphp
        
        @if($primaryImage)
            {{-- Primary image from rental-image-library --}}
            <img src="{{ asset('storage/' . $primaryImage->path) }}" 
                 alt="{{ $rental->title }}" 
                 class="rental-card-img" 
                 loading="lazy">
        @elseif(isset($legacyImageUrl))
            {{-- Fallback to legacy images array --}}
            <img src="{{ $legacyImageUrl }}" 
                 alt="{{ $rental->title }}" 
                 class="rental-card-img" 
                 loading="lazy">
        @else
            {{-- Placeholder when no image available --}}
            <div class="d-flex flex-column align-items-center justify-content-center text-muted h-100">
                <i class="ti ti-package ti-xl mb-2"></i>
                <small class="text-center">Kein Bild<br>verfügbar</small>
            </div>
        @endif
        
        {{-- Image count indicator for multiple images --}}
        @if($rental->images()->count() > 1)
            <div class="image-count-badge">
                <i class="ti ti-photo"></i> {{ $rental->images()->count() }}
            </div>
        @endif
    </div>
    <div class="card-body" style="flex: 1;">
        <div class="d-flex justify-content-between">
            <h5 class="card-title text-heading fw-semibold">{{ Illuminate\Support\Str::limit($rental->title, 40) }}</h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary me-2">
                    <!-- price depends on settings in price_ranges_id of current rental -->
                    @if($rental->price_ranges_id == 1)
                        <!-- Stundenpreis -->
                        {{ $rental->price_range_hour }}€/Stunde
                    @elseif($rental->price_ranges_id == 2)
                        <!-- Tagespreis -->
                        {{ $rental->price_range_day }}€/Tag
                    @elseif($rental->price_ranges_id == 3)
                        <!-- Einmalpreis -->
                        {{ $rental->price_range_one_time }}€
                    @elseif($rental->price_ranges_id == 4)
                        <!-- Saisonpreis -->
                        {{ $rental->price_range_season }}€/Saison
                    @endif
                </span>
                <button class="btn btn-icon btn-outline-danger btn-sm favorite-btn" data-rental-id="{{ $rental->id }}">
                    <i class="ti ti-heart"></i>
                </button>
            </div>
        </div>
                <p class="card-text small text-body">
            <i class="ti ti-map-pin"></i> 
            @if($rental->additionalLocations->count() > 0)
                mehrere Standorte
            @elseif($rental->location)
                {{ $rental->location->city }}, {{ $rental->location->postcode }}
            @else
                Standort nicht verfügbar
            @endif
        </p>
        <div class="mb-2">
            <span class="small text-muted">{{ $rental->category->name ?? 'Kategorie' }}</span>
        </div>
        @if($rental->user)
            <div class="mb-2">
                <small class="text-muted">
                    von <a href="{{ route('vendor.profile', $rental->user->id) }}" class="text-decoration-none">
                        {{ $rental->user->company_name ?: $rental->user->name }}
                    </a>
                </small>
            </div>
        @endif
        <div class="d-flex gap-2">
            <a href="{{ route('rentals.show', $rental->id) }}"
                class="btn btn-outline-primary flex-fill waves-effect">Details ansehen</a>
            @if($rental->user)
                <a href="{{ route('vendor.profile', $rental->user->id) }}"
                    class="btn btn-outline-secondary btn-sm waves-effect" title="Anbieter-Profil">
                    <i class="ti ti-user"></i>
                </a>
            @endif
        </div>
    </div>
</div>

@once
<style>
    /* Rental Card Image Styles */
    .rental-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .rental-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }
    
    .rental-card-image {
        border-radius: 0.5rem 0 0 0.5rem;
        flex-shrink: 0;
    }
    
    .rental-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0.5rem 0 0 0.5rem;
        transition: transform 0.3s ease;
    }
    
    .rental-card:hover .rental-card-img {
        transform: scale(1.05);
    }
    
    .image-count-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .image-count-badge i {
        font-size: 0.875rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .rental-card {
            flex-direction: column !important;
        }
        
        .rental-card-image {
            width: 100% !important;
            height: 200px !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        
        .rental-card-img {
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
    }
</style>
@endonce