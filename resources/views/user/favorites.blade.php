@extends('layouts/contentNavbarLayout')

@section('title', 'Meine Favoriten')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Meine Favoriten</h5>
      </div>
      <div class="card-body">
        <div class="text-center py-5">
          <i class="ti ti-heart" style="font-size: 4rem; color: #d1d5db;"></i>
          <h5 class="mt-3 mb-2">Noch keine Favoriten</h5>
          <p class="text-muted mb-4">
            Hier werden Ihre als Favoriten markierten Artikel angezeigt.
          </p>
          <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="ti ti-search me-1"></i>
            Artikel durchstöbern
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
