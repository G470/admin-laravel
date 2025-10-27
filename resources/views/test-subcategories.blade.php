@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Unterkategorien Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Unterkategorien Navigation Test</h1>

            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Kategorien mit Unterkategorien</h3>
                    <p class="text-muted">Diese Kategorien haben Unterkategorien und zeigen die Navigation an.</p>

                    @php
                        // Kategorien mit Unterkategorien laden
                        $categoriesWithChildren = \App\Models\Category::with([
                            'children' => function ($query) {
                                $query->withCount('rentals')
                                    ->where('status', 'online')
                                    ->orderBy('name');
                            }
                        ])
                            ->where('status', 'online')
                            ->whereHas('children')
                            ->orderBy('name')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($categoriesWithChildren->count() > 0)
                        @foreach($categoriesWithChildren as $category)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>{{ $category->name }}</h5>
                                    <small class="text-muted">
                                        {{ $category->children->count() }} Unterkategorien
                                    </small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Unterkategorien Navigation:</h6>
                                            @if($category->children->count() > 0)
                                                <div class="sub-categories-list">
                                                    @foreach($category->children as $subCategory)
                                                        <a href="{{ route('category.show', $subCategory->slug) }}"
                                                            class="d-block text-decoration-none mb-2 p-2 rounded border-hover">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ti ti-chevron-right me-2 text-primary"></i>
                                                                <span class="text-body">{{ $subCategory->name }}</span>
                                                                @if($subCategory->rentals_count > 0)
                                                                    <span
                                                                        class="badge bg-primary ms-auto">{{ $subCategory->rentals_count }}</span>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">Keine Unterkategorien vorhanden.</p>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <h6>Kategorie Details:</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>ID:</strong> {{ $category->id }}</li>
                                                <li><strong>Slug:</strong> {{ $category->slug ?? 'kein Slug' }}</li>
                                                <li><strong>Type:</strong> {{ $category->type ?? 'kein Type' }}</li>
                                                <li><strong>Status:</strong> {{ $category->status }}</li>
                                                <li><strong>Unterkategorien:</strong> {{ $category->children->count() }}</li>
                                            </ul>

                                            @if($category->children->count() > 0)
                                                <h6>Unterkategorien Details:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Slug</th>
                                                                <th>Vermietungen</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($category->children as $subCategory)
                                                                <tr>
                                                                    <td>{{ $subCategory->name }}</td>
                                                                    <td>{{ $subCategory->slug ?? 'kein Slug' }}</td>
                                                                    <td>
                                                                        @if($subCategory->rentals_count > 0)
                                                                            <span
                                                                                class="badge bg-success">{{ $subCategory->rentals_count }}</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">0</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-{{ $subCategory->status === 'online' ? 'success' : 'warning' }}">
                                                                            {{ $subCategory->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <h5>Keine Kategorien mit Unterkategorien gefunden</h5>
                            <p>Es wurden keine Kategorien mit Unterkategorien in der Datenbank gefunden.</p>
                        </div>
                    @endif
                </div>

                <div class="col-12">
                    <h3>Statistiken</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-primary">{{ \App\Models\Category::count() }}</h4>
                                    <p class="mb-0">Gesamt Kategorien</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-success">{{ \App\Models\Category::where('status', 'online')->count() }}
                                    </h4>
                                    <p class="mb-0">Online Kategorien</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-info">{{ \App\Models\Category::whereNotNull('parent_id')->count() }}
                                    </h4>
                                    <p class="mb-0">Unterkategorien</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-warning">{{ \App\Models\Category::whereHas('children')->count() }}</h4>
                                    <p class="mb-0">Kategorien mit Kindern</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection