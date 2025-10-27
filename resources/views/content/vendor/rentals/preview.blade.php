@extends('layouts/contentNavbarLayout')

@section('title', 'Objektvorschau')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
    <style>
        .img-preview-main {
            height: 400px;
            object-fit: cover;
            width: 100%;
        }

        .img-preview-thumb {
            height: 80px;
            object-fit: cover;
            cursor: pointer;
        }

        .thumbnail-wrapper {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .doc-link {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background-color: #f5f5f9;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }

        .doc-link:hover {
            background-color: #e7e7ff;
            color: #696cff;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            const swiperThumbs = new Swiper('.thumbnail-swiper', {
                spaceBetween: 10,
                slidesPerView: 'auto',
                freeMode: true,
                watchSlidesProgress: true,
            });

            const swiperMain = new Swiper('.main-swiper', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: swiperThumbs
                }
            });

            $('.date-picker').flatpickr({
                minDate: "today",
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d"
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor / <a href="{{ route('vendor.rentals.index') }}">Vermietungsobjekte</a>
            /</span>
        Objektvorschau
    </h4>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ferienhaus am See #{{ $id }} (Vorschau)</h5>
                    <div>
                        <a href="{{ route('vendor-rental-edit', ['id' => $id]) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Bearbeiten
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bildergalerie -->
                    <div class="mb-4">
                        <!-- Hauptbild Slider -->
                        <div class="swiper main-swiper mb-3">
                            <div class="swiper-wrapper">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="swiper-slide">
                                        <img src="{{asset('assets/img/backgrounds/' . $i . '.jpg')}}"
                                            class="img-fluid img-preview-main rounded" alt="Objekt Bild {{ $i }}">
                                    </div>
                                @endfor
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>

                        <!-- Thumbnails -->
                        <div class="swiper thumbnail-swiper">
                            <div class="swiper-wrapper">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="swiper-slide" style="width: 80px; margin-right: 10px;">
                                        <img src="{{asset('assets/img/backgrounds/' . $i . '.jpg')}}"
                                            class="img-fluid img-preview-thumb rounded" alt="Thumbnail {{ $i }}">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Objekt Details -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h3 class="mb-3">Ferienhaus am See #{{ $id }}</h3>
                            <div class="d-flex flex-wrap mb-3">
                                <span class="badge bg-label-primary me-2 mb-1">Ferienhaus</span>
                                <span class="badge bg-label-info me-2 mb-1">Direkt am See</span>
                                <span class="badge bg-label-success me-2 mb-1">Aktiv</span>
                            </div>

                            <div class="mb-4">
                                <h5>Beschreibung</h5>
                                <p>Dieses gemütliche Ferienhaus liegt direkt am Ufer des Sees und bietet einen
                                    atemberaubenden Blick auf das Wasser und die umliegenden Berge. Mit drei Schlafzimmern,
                                    zwei Badezimmern und einem großzügigen Wohnbereich ist es ideal für Familien oder
                                    Gruppen von bis zu 6 Personen.</p>
                                <p>Das Haus verfügt über eine voll ausgestattete Küche, einen Grill auf der Terrasse und
                                    einen Steg mit eigenem Bootsanleger. Genießen Sie Ihren Morgenkaffe auf der Veranda und
                                    beobachten Sie die aufgehende Sonne über dem See.</p>
                            </div>

                            <div class="mb-4">
                                <h5>Eigenschaften</h5>
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-home ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Fläche</small>
                                                <span>120 m²</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-bed ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Schlafzimmer</small>
                                                <span>3</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-bath ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Badezimmer</small>
                                                <span>2</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-users ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Max. Personen</small>
                                                <span>6</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-car ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Parkplätze</small>
                                                <span>2</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon bg-label-primary me-2">
                                                <i class="ti ti-wifi ti-sm"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">WLAN</small>
                                                <span>Kostenlos</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Lage</h5>
                                <p><i class="ti ti-map-pin me-1"></i> München, Leopoldstraße 123</p>
                                <div class="border rounded"
                                    style="height: 200px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-muted">Karte würde hier angezeigt werden</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Mietbedingungen</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Mindestaufenthalt</span>
                                        <span>2 Nächte</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Check-in</span>
                                        <span>15:00-20:00 Uhr</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Check-out</span>
                                        <span>bis 11:00 Uhr</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Kaution</span>
                                        <span>250€</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Haustiere</span>
                                        <span>Auf Anfrage (+15€ pro Nacht)</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Rauchen</span>
                                        <span>Nicht erlaubt</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h5>Dokumente</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="doc-link text-decoration-none">
                                            <i class="ti ti-file-text me-2"></i> AGB (agb-ferienhaus-{{ $id }}.pdf)
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="doc-link text-decoration-none">
                                            <i class="ti ti-file-description me-2"></i> Spezifikationen
                                            (spezifikationen-ferienhaus-{{ $id }}.pdf)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Preise</h5>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>Stundenpreis</span>
                                            <span>25,00 €</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>Tagespreis</span>
                                            <span class="fw-semibold">180,00 €</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>Servicegebühr</span>
                                            <span>35,00 €</span>
                                        </li>
                                    </ul>

                                    <div class="alert alert-warning mb-3">
                                        <div class="d-flex">
                                            <i class="ti ti-alert-circle me-2"></i>
                                            <div>
                                                <h6 class="alert-heading mb-0">Saisonpreise</h6>
                                                <div>20.07.{{ date('Y') }} - 10.08.{{ date('Y') }}: 240,00 € pro Tag</div>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="card-title mb-3">Verfügbarkeit prüfen</h5>
                                    <div class="mb-3">
                                        <label for="check_in" class="form-label">Check-in</label>
                                        <input type="text" class="form-control date-picker" id="check_in"
                                            placeholder="Datum auswählen">
                                    </div>
                                    <div class="mb-3">
                                        <label for="check_out" class="form-label">Check-out</label>
                                        <input type="text" class="form-control date-picker" id="check_out"
                                            placeholder="Datum auswählen">
                                    </div>
                                    <div class="mb-3">
                                        <label for="guests" class="form-label">Anzahl Personen</label>
                                        <select class="form-select" id="guests">
                                            <option value="1">1 Person</option>
                                            <option value="2" selected>2 Personen</option>
                                            <option value="3">3 Personen</option>
                                            <option value="4">4 Personen</option>
                                            <option value="5">5 Personen</option>
                                            <option value="6">6 Personen</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary d-grid w-100">Verfügbarkeit prüfen</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection