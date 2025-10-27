@extends('layouts/contentNavbarLayout')

@section('title', 'Rechnungen')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Rechnungsverwaltung</h4>
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary me-1" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i data-feather="filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i data-feather="download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <livewire:admin.bills-table />
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:admin.bills-filter />
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @livewire('admin.bills-export')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {         // Initialize Select2         $('.select2').select2({             theme: 'bootstrap-5'         });
        // Initialize DataTable         $('.table').DataTable({             language: {                 url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/German.json'             },             dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-2 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',             responsive: true,             buttons: [                 {                     extend: 'collection',                     className: 'btn btn-label-secondary dropdown-toggle me-2',                     text: '<i class="ti ti-download me-1"></i> Export',                     buttons: [                         {                             extend: 'print',                             text: '<i class="ti ti-printer me-1"></i> Print',                             className: 'dropdown-item',                             exportOptions: { columns: [0, 1, 2, 3, 4, 5] }                         },                         {                             extend: 'csv',                             text: '<i class="ti ti-file-text me-1"></i> CSV',                             className: 'dropdown-item',                             exportOptions: { columns: [0, 1, 2, 3, 4, 5] }                         },                         {                             extend: 'excel',                             text: '<i class="ti ti-file-spreadsheet me-1"></i> Excel',                             className: 'dropdown-item',                             exportOptions: { columns: [0, 1, 2, 3, 4, 5] }                         },                         {                             extend: 'pdf',                             text: '<i class="ti ti-file me-1"></i> PDF',                             className: 'dropdown-item',                             exportOptions: { columns: [0, 1, 2, 3, 4, 5] }                         }                     ]                 }             ]         });     });
    </script>
@endsection