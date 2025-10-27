@extends('layouts/contentNavbarLayoutFrontend')

@section('title', $category->name)

@section('content')
<section class="bg-body py-3 border-bottom mb-4">
<div class="container">
    @livewire('search-form')
</div>
</section>

    <section>
        <div class="container">
            <!-- breadcrumb -->
            @livewire('frontend.breadcrumb', [
                'category' => $category,
                'autoGenerateFromCategory' => true,
                'showHome' => true,
                'homeText' => 'Startseite'
            ])

            <h1 class="mb-4 fw-semibold">{{ $category->name }}</h1>
            <!-- category filter -->
            <div class="row">
                <div class="col-4">
                    <!-- display all sub categories if category has sub categories -->
                    @if($category->children->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-category me-2"></i>
                                    Unterkategorien
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="sub-categories-list">
                                    @foreach($category->children as $subCategory)
                                        <a href="{{ route('category.show', $subCategory->slug) }}" 
                                           class="d-block text-decoration-none mb-2 p-2 rounded border-hover">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-chevron-right me-2 text-primary"></i>
                                                <span class="text-body">{{ $subCategory->name }}</span>
                                                @if($subCategory->rentals_count > 0)
                                                    <span class="badge bg-primary ms-auto">{{ $subCategory->rentals_count }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- dynamic fields will be loaded here -->
                    <div class="form-group">
                        <!-- dynamic fields will be loaded here -->
                        <div id="dynamic-fields-container">
                            
                            @livewire('rental-field-filter', ['categoryId' => $category->id ?? null])
                            {{-- Dynamic fields will be loaded here when category is selected --}}
                        </div>
                    </div>

                </div>

                <div class="col-8">
                    @livewire('frontend.rental-list', ['category' => $category])
                </div>
            </div>


        </div>
    </section>
@endsection