@extends('layouts/contentNavbarLayout')

@section('title', 'Vermietungsobjekte verwalten')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Admin /</span> Vermietungsobjekte verwalten
</h4>

@livewire('admin.rental-table')

@endsection 