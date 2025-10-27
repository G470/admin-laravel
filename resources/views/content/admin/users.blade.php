@extends('layouts/contentNavbarLayout')

@section('title', 'Benutzerverwaltung')

    @section('vendor-style')
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    @endsection

    @section('vendor-script')
        <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    @endsection

    @section('page-script')
        <script src="{{asset('assets/js/app-user-list.js')}}"></script>
    @endsection

    @section('content')
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Benutzerverwaltung
        </h4>

        {{-- Statistics Dashboard --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-users ti-md"></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Gesamt Benutzer</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['total_users']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class="ti ti-user-check ti-md"></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Aktive Benutzer</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['active_users']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-building ti-md"></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Vermieter</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['vendors']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-label-info rounded">
                                    <i class="ti ti-user-plus ti-md"></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Neue diese Woche</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['new_users_this_week']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-3">Filteroptionen</h5>
                    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                        <i class="ti ti-plus me-1"></i>
                        Neuer Benutzer
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Benutzer</th>
                            <th>Rolle</th>
                            <th>Plan</th>
                            <th>Objekte</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                @if($user->profile_image)
                                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-{{ ['primary', 'success', 'danger', 'warning', 'info'][$loop->index % 5] }}">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $user->name }}</span>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            @if($user->company_name)
                                                <small class="text-muted">{{ $user->company_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-label-danger me-1">
                                            Admin
                                        </span>
                                    @endif
                                    @if($user->is_vendor)
                                        <span class="badge bg-label-primary me-1">
                                            Vendor
                                        </span>
                                    @endif
                                    @if(!$user->is_admin && !$user->is_vendor)
                                        <span class="text-muted">Benutzer</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->subscription_plan)
                                        <span class="text-truncate">{{ ucfirst($user->subscription_plan) }}</span>
                                    @else
                                        <span class="text-muted">Kein Plan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_vendor)
                                        <span class="badge bg-label-info">Vermieter</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-label-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-label-danger">Inaktiv</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-inline-block">
                                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">
                                                <i class="ti ti-eye me-1"></i>Details
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                                <i class="ti ti-edit me-1"></i>Bearbeiten
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ti ti-{{ $user->email_verified_at ? 'user-off' : 'user-check' }} me-1"></i>
                                                        {{ $user->email_verified_at ? 'Deaktivieren' : 'Aktivieren' }}
                                                    </button>
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ti ti-trash me-1"></i>Löschen
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti ti-users-off ti-lg text-muted mb-2"></i>
                                        <span class="text-muted">Keine Benutzer gefunden</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Neuen Benutzer hinzufügen</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="add-user-name">Vollständiger Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="add-user-name" placeholder="Max Mustermann" name="name" required />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="add-user-email">E-Mail *</label>
                        <input type="email" id="add-user-email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="max@example.com" name="email" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="add-user-password">Passwort *</label>
                        <input type="password" id="add-user-password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="add-user-password-confirm">Passwort bestätigen *</label>
                        <input type="password" id="add-user-password-confirm" class="form-control" 
                               name="password_confirmation" required />
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Benutzerrolle</label>
                        <div class="form-check">
                            <input class="form-check-input @error('is_admin') is-invalid @enderror" type="checkbox" 
                                   id="add-user-admin" name="is_admin" value="1" />
                            <label class="form-check-label" for="add-user-admin">
                                Administrator
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('is_vendor') is-invalid @enderror" type="checkbox" 
                                   id="add-user-vendor" name="is_vendor" value="1" />
                            <label class="form-check-label" for="add-user-vendor">
                                Vermieter
                            </label>
                        </div>
                        @error('is_admin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('is_vendor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="add-user-phone">Telefon</label>
                        <input type="text" id="add-user-phone" class="form-control @error('phone') is-invalid @enderror" 
                               placeholder="+49 123 456789" name="phone" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="add-user-profile-image">Profilbild</label>
                        <input type="file" id="add-user-profile-image" class="form-control @error('profile_image') is-invalid @enderror" 
                               name="profile_image" accept="image/*" />
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Benutzer erstellen</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Abbrechen</button>
                </form>
            </div>
        </div>
    </div>
@endsection