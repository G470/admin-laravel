@props([
    'latitude' => null,
    'longitude' => null,
    'address' => null,
    'height' => '400px',
    'width' => '100%',
    'zoom' => 15,
    'markerTitle' => 'Standort',
    'showControls' => true,
    'draggable' => false,
    'clickable' => false,
    'onLocationSelect' => null
])

@php
    $mapsService = app(\App\Services\MapsService::class);
    $serviceStatus = $mapsService->getServiceStatus();
    $hasMapsService = $serviceStatus['has_any_service'];
    $preferredService = $serviceStatus['preferred_service'];
@endphp

<div class="maps-display-component" 
     data-latitude="{{ $latitude }}" 
     data-longitude="{{ $longitude }}"
     data-address="{{ $address }}"
     data-zoom="{{ $zoom }}"
     data-marker-title="{{ $markerTitle }}"
     data-show-controls="{{ $showControls ? 'true' : 'false' }}"
     data-draggable="{{ $draggable ? 'true' : 'false' }}"
     data-clickable="{{ $clickable ? 'true' : 'false' }}"
     data-on-location-select="{{ $onLocationSelect }}"
     data-maps-service="{{ $preferredService }}"
     style="width: {{ $width }}; height: {{ $height }};">
    
    @if(!$hasMapsService)
        <div class="maps-display-error">
            <div class="alert alert-warning d-flex align-items-center">
                <i class="ti ti-map-off me-2"></i>
                <div>
                    <strong>Karten-Dienst nicht verfügbar</strong><br>
                    <small>Bitte konfigurieren Sie einen Karten-Dienst in den Admin-Einstellungen.</small>
                </div>
            </div>
        </div>
    @elseif(!$latitude || !$longitude)
        <div class="maps-display-placeholder">
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center text-muted">
                    <i class="ti ti-map-pin-off" style="font-size: 3rem;"></i>
                    <p class="mt-2">Keine Koordinaten verfügbar</p>
                    @if($address)
                        <small>{{ $address }}</small>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="maps-display-container">
            @if($preferredService === 'google_maps')
                <div id="google-map-{{ uniqid() }}" class="google-map" style="width: 100%; height: 100%;"></div>
            @elseif($preferredService === 'openstreetmap')
                <div id="osm-map-{{ uniqid() }}" class="osm-map" style="width: 100%; height: 100%;"></div>
            @endif
            
            <div class="maps-display-overlay">
                <div class="maps-service-indicator">
                    <small class="badge bg-secondary">
                        @if($preferredService === 'google_maps')
                            <i class="ti ti-brand-google me-1"></i>Google Maps
                        @elseif($preferredService === 'openstreetmap')
                            <i class="ti ti-map me-1"></i>OpenStreetMap
                        @endif
                    </small>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapsComponents = document.querySelectorAll('.maps-display-component');
    
    mapsComponents.forEach(function(component) {
        const latitude = parseFloat(component.dataset.latitude);
        const longitude = parseFloat(component.dataset.longitude);
        const zoom = parseInt(component.dataset.zoom);
        const markerTitle = component.dataset.markerTitle;
        const showControls = component.dataset.showControls === 'true';
        const draggable = component.dataset.draggable === 'true';
        const clickable = component.dataset.clickable === 'true';
        const onLocationSelect = component.dataset.onLocationSelect;
        const mapsService = component.dataset.mapsService;
        
        if (!latitude || !longitude) return;
        
        if (mapsService === 'google_maps') {
            initializeGoogleMap(component, latitude, longitude, zoom, markerTitle, showControls, draggable, clickable, onLocationSelect);
        } else if (mapsService === 'openstreetmap') {
            initializeOpenStreetMap(component, latitude, longitude, zoom, markerTitle, showControls, draggable, clickable, onLocationSelect);
        }
    });
});

function initializeGoogleMap(component, latitude, longitude, zoom, markerTitle, showControls, draggable, clickable, onLocationSelect) {
    // Check if Google Maps API is loaded
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.warn('Google Maps API not loaded');
        return;
    }
    
    const mapElement = component.querySelector('.google-map');
    if (!mapElement) return;
    
    const mapOptions = {
        center: { lat: latitude, lng: longitude },
        zoom: zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: !showControls,
        zoomControl: showControls,
        mapTypeControl: showControls,
        scaleControl: showControls,
        streetViewControl: showControls,
        rotateControl: showControls,
        fullscreenControl: showControls
    };
    
    const map = new google.maps.Map(mapElement, mapOptions);
    
    // Add marker
    const marker = new google.maps.Marker({
        position: { lat: latitude, lng: longitude },
        map: map,
        title: markerTitle,
        draggable: draggable
    });
    
    // Add info window
    const infoWindow = new google.maps.InfoWindow({
        content: `<div><strong>${markerTitle}</strong><br>${latitude.toFixed(6)}, ${longitude.toFixed(6)}</div>`
    });
    
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
    
    // Handle map click for location selection
    if (clickable && onLocationSelect) {
        map.addListener('click', function(event) {
            const newLat = event.latLng.lat();
            const newLng = event.latLng.lng();
            
            // Update marker position
            marker.setPosition(event.latLng);
            
            // Call callback function
            if (typeof window[onLocationSelect] === 'function') {
                window[onLocationSelect](newLat, newLng);
            }
        });
    }
    
    // Handle marker drag
    if (draggable && onLocationSelect) {
        marker.addListener('dragend', function(event) {
            const newLat = event.latLng.lat();
            const newLng = event.latLng.lng();
            
            if (typeof window[onLocationSelect] === 'function') {
                window[onLocationSelect](newLat, newLng);
            }
        });
    }
}

function initializeOpenStreetMap(component, latitude, longitude, zoom, markerTitle, showControls, draggable, clickable, onLocationSelect) {
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.warn('Leaflet not loaded');
        return;
    }
    
    const mapElement = component.querySelector('.osm-map');
    if (!mapElement) return;
    
    const map = L.map(mapElement).setView([latitude, longitude], zoom);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add marker
    const marker = L.marker([latitude, longitude], {
        draggable: draggable,
        title: markerTitle
    }).addTo(map);
    
    // Add popup
    marker.bindPopup(`<div><strong>${markerTitle}</strong><br>${latitude.toFixed(6)}, ${longitude.toFixed(6)}</div>`);
    
    // Handle map click for location selection
    if (clickable && onLocationSelect) {
        map.on('click', function(event) {
            const newLat = event.latlng.lat;
            const newLng = event.latlng.lng;
            
            // Update marker position
            marker.setLatLng([newLat, newLng]);
            
            // Call callback function
            if (typeof window[onLocationSelect] === 'function') {
                window[onLocationSelect](newLat, newLng);
            }
        });
    }
    
    // Handle marker drag
    if (draggable && onLocationSelect) {
        marker.on('dragend', function(event) {
            const newLat = event.target.getLatLng().lat;
            const newLng = event.target.getLatLng().lng;
            
            if (typeof window[onLocationSelect] === 'function') {
                window[onLocationSelect](newLat, newLng);
            }
        });
    }
}

// Global function to handle location selection
function handleLocationSelect(latitude, longitude) {
    console.log('Location selected:', latitude, longitude);
    // This function can be overridden by the parent component
}
</script>
@endpush

@push('styles')
<style>
.maps-display-component {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.maps-display-error,
.maps-display-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.maps-display-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.maps-display-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

.maps-service-indicator {
    background: rgba(255, 255, 255, 0.9);
    padding: 4px 8px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.google-map,
.osm-map {
    border-radius: 0.5rem;
}

/* Loading state */
.maps-display-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background-color: #f8f9fa;
}

.maps-display-loading .spinner-border {
    width: 2rem;
    height: 2rem;
}
</style>
@endpush 