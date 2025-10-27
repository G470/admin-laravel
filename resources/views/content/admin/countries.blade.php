@extends('layouts/layoutMaster')

@section('title', 'Länder-Verwaltung - Admin')


@section('page-script')
    <script>
        // Toast notification handling
        document.addEventListener('livewire:init', () => {
            Livewire.on('success', (data) => {
                if (typeof window.showToast === 'function') {
                    window.showToast('success', data[0].message);
                } else {
                    console.log('Success:', data[0].message);
                }
            });

            Livewire.on('error', (data) => {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', data[0].message);
                } else {
                    console.error('Error:', data[0].message);
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="ti ti-world text-primary me-2"></i>Länder-Verwaltung
                            </h5>
                            <small class="text-muted">Verwaltung der verfügbaren Länder für Standorte und Benutzer</small>
                        </div>
                        <div>
                            <span class="badge bg-label-info">Systemdaten</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Countries Management Component -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @livewire('admin.countries')
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="ti ti-info-circle text-info me-3 mt-1" style="font-size: 1.2rem;"></i>
                            <div>
                                <h6 class="mb-2 text-info">Wichtige Hinweise zur Länder-Verwaltung</h6>
                                <ul class="mb-0 small text-muted">
                                    <li><strong>Ländercode:</strong> Muss genau 2 Zeichen (ISO 3166-1 Alpha-2) haben (z.B.
                                        DE, AT, CH)</li>
                                    <li><strong>Löschabsicherung:</strong> Länder mit verknüpften Standorten können nicht
                                        gelöscht werden</li>
                                    <li><strong>Aktivierungsstatus:</strong> Inaktive Länder werden in neuen Formularen
                                        nicht angezeigt</li>
                                    <li><strong>Telefonvorwahl:</strong> Optional, Format z.B. +49, +43, +41</li>
                                    <li><strong>Verwendung:</strong> Länder werden für Standorte, Benutzerprofile und
                                        Kontaktdaten verwendet</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection