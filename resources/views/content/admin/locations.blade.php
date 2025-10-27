@extends('layouts/contentNavbarLayout')

@section('title', 'Standorte verwalten - Admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @livewire('admin.locations')
    </div>
@endsection