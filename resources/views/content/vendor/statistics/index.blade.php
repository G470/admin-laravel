@extends('layouts/contentNavbarLayout')

@section('title', 'Statistiken')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // Initialisierung von Select2
            $('.select2').select2();

            // Initialisierung von Flatpickr
            $('#dateRange').flatpickr({
                mode: 'range',
                maxDate: 'today',
                dateFormat: 'd.m.Y',
                defaultDate: [new Date().fp_incr(-30), new Date()]
            });

            // Umsatz-Diagramm
            const revenueChartEl = document.querySelector('#revenueChart');
            if (revenueChartEl) {
                const revenueChart = new ApexCharts(revenueChartEl, {
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#696cff', '#8592a3'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    dataLabels: {
                        enabled: false
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                            left: 0
                        }
                    },
                    markers: {
                        size: 5,
                        colors: 'transparent',
                        strokeColors: 'transparent',
                        strokeWidth: 4,
                        discrete: [],
                        hover: {
                            size: 7
                        }
                    },
                    series: [
                        {
                            name: 'Umsatz',
                            data: [1500, 1800, 1350, 1200, 800, 1100, 1400, 1600, 1850, 1950, 2200, 2400, 1900, 2300, 2100, 1750, 1800, 2050, 2300, 2150, 2000, 2100, 2300, 2550, 2700, 2900, 3100, 3300, 3200, 3000]
                        }
                    ],
                    xaxis: {
                        categories: Array.from({ length: 30 }, (_, i) => {
                            const date = new Date();
                            date.setDate(date.getDate() - 30 + i + 1);
                            return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' });
                        }),
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            show: true,
                            style: {
                                fontSize: '13px',
                                colors: '#697a8d'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            formatter: function (val) {
                                return val.toFixed(0) + ' €';
                            }
                        }
                    },
                    tooltip: {
                        x: {
                            show: false
                        },
                        y: {
                            formatter: function (val) {
                                return val.toFixed(2) + ' €';
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            shadeIntensity: 0.4,
                            opacityFrom: 0.8,
                            opacityTo: 0.1,
                            stops: [0, 95, 100]
                        }
                    },
                    responsive: [
                        {
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 250
                                }
                            }
                        }
                    ]
                });
                revenueChart.render();
            }

            // Donut-Diagramm für Objekttypen
            const objectTypesChartEl = document.querySelector('#objectTypesChart');
            if (objectTypesChartEl) {
                const objectTypesChart = new ApexCharts(objectTypesChartEl, {
                    chart: {
                        height: 280,
                        type: 'donut'
                    },
                    series: [42, 25, 18, 15],
                    labels: ['Ferienhäuser', 'Ferienwohnungen', 'Seminarräume', 'Transportfahrzeuge'],
                    colors: ['#696cff', '#03c3ec', '#ff6633', '#ffab00'],
                    stroke: {
                        width: 0
                    },
                    dataLabels: {
                        enabled: false,
                        formatter: function (val, opt) {
                            return opt.w.globals.series[opt.seriesIndex] + '%';
                        }
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        theme: false
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                            right: 0,
                            left: 0
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '0.9rem',
                                        fontFamily: 'Public Sans',
                                        fontWeight: 600
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '1.5rem',
                                        fontFamily: 'Public Sans',
                                        fontWeight: 500,
                                        color: '#697a8d',
                                        formatter: function (val) {
                                            return val + '%';
                                        }
                                    },
                                    total: {
                                        show: true,
                                        fontSize: '1.5rem',
                                        fontWeight: 500,
                                        label: 'Gesamt',
                                        color: '#697a8d',
                                        formatter: function (w) {
                                            return '100%';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    responsive: [
                        {
                            breakpoint: 420,
                            options: {
                                chart: {
                                    height: 230
                                }
                            }
                        }
                    ]
                });
                objectTypesChart.render();
            }

            // Balkendiagramm für Buchungen pro Objekt
            const bookingsPerObjectChartEl = document.querySelector('#bookingsPerObjectChart');
            if (bookingsPerObjectChartEl) {
                const bookingsPerObjectChart = new ApexCharts(bookingsPerObjectChartEl, {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        }
                    },
                    colors: ['#696cff'],
                    series: [
                        {
                            name: 'Buchungen',
                            data: [18, 15, 13, 11, 8]
                        }
                    ],
                    xaxis: {
                        categories: ['Ferienhaus am See #1', 'Ferienwohnung Zentral #2', 'Ferienhaus am See #3', 'Transportfahrzeug #1', 'Seminarraum Business #1'],
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#697a8d',
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Anzahl der Buchungen',
                            style: {
                                fontSize: '13px',
                                fontWeight: 500
                            }
                        },
                        labels: {
                            style: {
                                colors: '#697a8d',
                                fontSize: '13px'
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    dataLabels: {
                        enabled: false
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        padding: {
                            top: 0,
                            bottom: 0,
                            left: 0
                        }
                    },
                    responsive: [
                        {
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 250
                                },
                                plotOptions: {
                                    bar: {
                                        borderRadius: 2
                                    }
                                }
                            }
                        }
                    ]
                });
                bookingsPerObjectChart.render();
            }
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Statistiken
    </h4>

    <!-- Filter-Optionen -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="dateRange" class="form-label">Zeitraum</label>
                    <input type="text" class="form-control" id="dateRange" placeholder="Zeitraum auswählen">
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="objectFilter" class="form-label">Objekt</label>
                    <select id="objectFilter" class="select2 form-select" data-placeholder="Alle Objekte">
                        <option value="all">Alle Objekte</option>
                        <option value="1">Ferienhaus am See #1</option>
                        <option value="2">Ferienwohnung Zentral #2</option>
                        <option value="3">Ferienhaus am See #3</option>
                        <option value="4">Transportfahrzeug #1</option>
                        <option value="5">Seminarraum Business #1</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary">Filter anwenden</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Übersichtskarten -->
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-0">Umsatz</h5>
                            <small class="text-muted">Letzten 30 Tage</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="ti ti-currency-euro ti-sm"></i>
                            </div>
                        </div>
                    </div>
                    <h2 class="mb-1">54.350 €</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-success me-2">+12.5%</span>
                        <small class="text-muted">vs. Vormonat</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-0">Buchungen</h5>
                            <small class="text-muted">Letzten 30 Tage</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="ti ti-calendar-event ti-sm"></i>
                            </div>
                        </div>
                    </div>
                    <h2 class="mb-1">65</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-success me-2">+8.2%</span>
                        <small class="text-muted">vs. Vormonat</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-0">Auslastung</h5>
                            <small class="text-muted">Durchschnitt</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ti ti-chart-pie ti-sm"></i>
                            </div>
                        </div>
                    </div>
                    <h2 class="mb-1">72%</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-success me-2">+5.8%</span>
                        <small class="text-muted">vs. Vormonat</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-0">Bewertung</h5>
                            <small class="text-muted">Durchschnitt</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ti ti-star ti-sm"></i>
                            </div>
                        </div>
                    </div>
                    <h2 class="mb-1">4.8/5</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-success me-2">+0.2</span>
                        <small class="text-muted">vs. Vormonat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Umsatzdiagramm -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Umsatzentwicklung</h5>
                        <small class="text-muted">Letzten 30 Tage</small>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-calendar me-1"></i> <span>30 Tage</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0);">7 Tage</a></li>
                            <li><a class="dropdown-item active" href="javascript:void(0);">30 Tage</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">3 Monate</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">6 Monate</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">1 Jahr</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="revenueChart"></div>
                </div>
            </div>
        </div>

        <!-- Objekt-Verteilung -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Objekt-Verteilung</h5>
                    <small class="text-muted">nach Umsatzanteil</small>
                </div>
                <div class="card-body">
                    <div id="objectTypesChart"></div>
                    <div class="mt-3 pt-1 text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <div>
                                <div class="badge rounded-pill p-1" style="background-color: #696cff"></div>
                                <span>Ferienhäuser</span>
                            </div>
                            <div>
                                <div class="badge rounded-pill p-1" style="background-color: #03c3ec"></div>
                                <span>Ferienwohnungen</span>
                            </div>
                            <div>
                                <div class="badge rounded-pill p-1" style="background-color: #ff6633"></div>
                                <span>Seminarräume</span>
                            </div>
                            <div>
                                <div class="badge rounded-pill p-1" style="background-color: #ffab00"></div>
                                <span>Transportfahrzeuge</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buchungs-Statistik und Top-Objekte -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Buchungen pro Objekt</h5>
                    <small class="text-muted">Letzten 30 Tage</small>
                </div>
                <div class="card-body">
                    <div id="bookingsPerObjectChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Top bewertete Objekte</h5>
                        <small class="text-muted">Basierend auf Gästebewertungen</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Objekt</th>
                                    <th>Bewertung</th>
                                    <th>Buchungen</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{asset('assets/img/backgrounds/1.jpg')}}" alt="Objekt"
                                                class="me-2 rounded" width="42" height="32" style="object-fit: cover;">
                                            <div>Ferienhaus am See #1</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-1">4.9</div>
                                            <div class="text-warning">
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>18</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{asset('assets/img/backgrounds/2.jpg')}}" alt="Objekt"
                                                class="me-2 rounded" width="42" height="32" style="object-fit: cover;">
                                            <div>Ferienwohnung Zentral #2</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-1">4.8</div>
                                            <div class="text-warning">
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{asset('assets/img/backgrounds/3.jpg')}}" alt="Objekt"
                                                class="me-2 rounded" width="42" height="32" style="object-fit: cover;">
                                            <div>Seminarraum Business #1</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-1">4.7</div>
                                            <div class="text-warning">
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-half-filled"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>8</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{asset('assets/img/backgrounds/4.jpg')}}" alt="Objekt"
                                                class="me-2 rounded" width="42" height="32" style="object-fit: cover;">
                                            <div>Ferienhaus am See #3</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-1">4.6</div>
                                            <div class="text-warning">
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-half-filled"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>13</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{asset('assets/img/backgrounds/5.jpg')}}" alt="Objekt"
                                                class="me-2 rounded" width="42" height="32" style="object-fit: cover;">
                                            <div>Transportfahrzeug #1</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-1">4.5</div>
                                            <div class="text-warning">
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-filled"></i>
                                                <i class="ti ti-star-half-filled"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>11</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actionbar -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center text-center text-md-start gap-3">
                        <div>
                            <h5 class="mb-1">Detaillierte Berichte</h5>
                            <p class="text-body mb-md-0">Laden Sie detaillierte Berichte für Ihre Objekte und Buchungen
                                herunter</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-label-secondary d-flex align-items-center">
                                <i class="ti ti-file-export me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">CSV Export</span>
                            </button>
                            <button class="btn btn-label-secondary d-flex align-items-center">
                                <i class="ti ti-file-spreadsheet me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Excel Export</span>
                            </button>
                            <button class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-file-text me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">PDF Bericht</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection