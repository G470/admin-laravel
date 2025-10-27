@extends('layouts/contentNavbarLayout')

@section('title', 'Bewertungen verwalten - Admin')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/select2/select2.css'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @livewire('admin.reviews')
                </div>
            </div>
        </div>
    </div>
@endsection