@if(isset($seoData) && !empty($seoData['default_text']))
    <div class="category-seo-content mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="seo-text-content">
                                {!! nl2br(e($seoData['default_text'])) !!}
                            </div>

                            @if(isset($location) && $location)
                                <div class="location-info mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="ti ti-map-pin me-1"></i>
                                        Standort: {{ $location->city }}
                                        @if($location->postcode)
                                            ({{ $location->postcode }})
                                        @endif
                                        @if($location->state)
                                            , {{ $location->state }}
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Meta Tags -->
    @push('head')
        @if(!empty($seoData['meta_title']))
            <title>{{ $seoData['meta_title'] }}</title>
            <meta property="og:title" content="{{ $seoData['meta_title'] }}">
        @endif

        @if(!empty($seoData['meta_description']))
            <meta name="description" content="{{ $seoData['meta_description'] }}">
            <meta property="og:description" content="{{ $seoData['meta_description'] }}">
        @endif

        <!-- Category specific meta tags -->
        @if(isset($category))
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{ url()->current() }}">

            @if($category->category_image)
                <meta property="og:image" content="{{ asset('storage/' . $category->category_image) }}">
            @endif
        @endif

        <!-- Location specific meta tags -->
        @if(isset($location) && $location)
            <meta name="geo.region" content="{{ $location->country ?? 'DE' }}-{{ $location->state }}">
            <meta name="geo.placename" content="{{ $location->city }}">
            @if($location->postcode)
                <meta name="geo.position" content="{{ $location->lat }};{{ $location->lng }}">
                <meta name="ICBM" content="{{ $location->lat }}, {{ $location->lng }}">
            @endif
        @endif
    @endpush

    <!-- Structured Data -->
    @push('head')
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebPage",
                "name": "{{ $seoData['meta_title'] ?? (isset($category) ? $category->name : 'Kategorie') }}",
                "description": "{{ $seoData['meta_description'] ?? '' }}",
                "url": "{{ url()->current() }}",
                @if(isset($category))
                    "mainEntity": {
                        "@type": "ProductGroup",
                        "name": "{{ $category->name }}",
                        "description": "{{ $category->description ?? $seoData['default_text'] ?? '' }}"
                        @if($category->category_image)
                            ,"image": "{{ asset('storage/' . $category->category_image) }}"
                        @endif
                    },
                @endif
                @if(isset($location) && $location)
                    "spatialCoverage": {
                        "@type": "Place",
                        "name": "{{ $location->city }}",
                        "address": {
                            "@type": "PostalAddress",
                            "addressLocality": "{{ $location->city }}",
                            "postalCode": "{{ $location->postcode }}",
                            "addressRegion": "{{ $location->state }}",
                            "addressCountry": "{{ $location->country ?? 'DE' }}"
                        }
                        @if($location->lat && $location->lng)
                            ,"geo": {
                                "@type": "GeoCoordinates",
                                "latitude": {{ $location->lat }},
                                "longitude": {{ $location->lng }}
                            }
                        @endif
                    },
                @endif
                "provider": {
                    "@type": "Organization",
                    "name": "Inlando",
                    "url": "{{ url('/') }}"
                }
            }
            </script>
    @endpush
@endif

<style>
    .category-seo-content {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        margin-top: 2rem;
    }

    .seo-text-content {
        line-height: 1.6;
        color: #495057;
        font-size: 0.95rem;
    }

    .seo-text-content h1,
    .seo-text-content h2,
    .seo-text-content h3 {
        color: #212529;
        margin-bottom: 1rem;
    }

    .location-info {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 4px;
        padding: 0.5rem;
    }

    @media (max-width: 768px) {
        .category-seo-content {
            margin-top: 1.5rem;
        }

        .seo-text-content {
            font-size: 0.9rem;
        }
    }
</style>