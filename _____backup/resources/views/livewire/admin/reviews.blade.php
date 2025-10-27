<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Bewertungen verwalten</h4>
        <div class="d-flex gap-2">
            <span class="badge bg-label-info">{{ $reviews->total() }} Gesamt</span>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Bewertungen durchsuchen..."
                            wire:model.debounce.500ms="search">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="statusFilter">
                        <option value="">Alle Status</option>
                        <option value="pending">Ausstehend</option>
                        <option value="approved">Genehmigt</option>
                        <option value="rejected">Abgelehnt</option>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="$set('search', '')">
                        <i class="ti ti-refresh"></i> Filter zurücksetzen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th wire:click="sortBy('id')" style="cursor: pointer;">
                            ID 
                            @if($sortBy === 'id')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th>Benutzer</th>
                        <th>Vermietung</th>
                        <th wire:click="sortBy('rating')" style="cursor: pointer;">
                            Bewertung 
                            @if($sortBy === 'rating')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th>Kommentar</th>
                        <th wire:click="sortBy('status')" style="cursor: pointer;">
                            Status 
                            @if($sortBy === 'status')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                            Datum 
                            @if($sortBy === 'created_at')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="text-muted small">{{ $review->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-label-primary rounded-circle">
                                            {{ substr($review->user->name ?? 'U', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $review->user->name ?? 'Unbekannt' }}</span><br>
                                        <small class="text-muted">{{ $review->user->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-medium">{{ $review->rental->title }}</span><br>
                                    <small class="text-muted">ID: {{ $review->rental_id }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="ti ti-star{{ $i <= ($review->rating ?? 0) ? '-filled text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 small">({{ $review->rating ?? 0 }}/5)</span>
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 200px;">
                                    <span title="{{ $review->comment }}">
                                        {{ $review->comment }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @switch($review->status ?? 'pending')
                                    @case('approved')
                                        <span class="badge bg-success">Genehmigt</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Abgelehnt</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning">Ausstehend</span>
                                @endswitch
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $review->created_at ? $review->created_at->format('d.m.Y H:i') : 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(($review->status ?? 'pending') === 'pending')
                                        <button type="button" class="btn btn-outline-success" 
                                            wire:click="approveReview({{ $review->id }})"
                                            title="Genehmigen">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            wire:click="rejectReview({{ $review->id }})"
                                            title="Ablehnen">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-danger" 
                                        wire:click="deleteReview({{ $review->id }})"
                                        onclick="confirm('Bewertung wirklich löschen?') || event.stopImmediatePropagation()"
                                        title="Löschen">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                @if($search || $statusFilter)
                                    <i class="ti ti-search-off fs-3 mb-2 d-block"></i>
                                    Keine Bewertungen gefunden für die aktuellen Filter
                                @else
                                    <i class="ti ti-star-off fs-3 mb-2 d-block"></i>
                                    Noch keine Bewertungen vorhanden
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
            <div class="card-footer">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- Statistics Summary -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-eye text-info fs-2 mb-2"></i>
                    <h6 class="card-title">Ausstehend</h6>
                    <h4 class="text-info">{{ $reviews->where('status', 'pending')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-check text-success fs-2 mb-2"></i>
                    <h6 class="card-title">Genehmigt</h6>
                    <h4 class="text-success">{{ $reviews->where('status', 'approved')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-x text-danger fs-2 mb-2"></i>
                    <h6 class="card-title">Abgelehnt</h6>
                    <h4 class="text-danger">{{ $reviews->where('status', 'rejected')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-star text-warning fs-2 mb-2"></i>
                    <h6 class="card-title">Durchschnitt</h6>
                    <h4 class="text-warning">{{ number_format($reviews->avg('rating') ?? 0, 1) }}/5</h4>
                </div>
            </div>
        </div>
    </div>
</div>
