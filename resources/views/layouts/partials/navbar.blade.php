<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Suchen...</span>
                </a>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Language -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-language rounded-circle ti-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'de' ? 'active' : '' }}"
                            href="{{ route('language.swap', 'de') }}">
                            <i class="ti ti-brand-github rounded-circle ti-sm me-2"></i>
                            <span class="align-middle">Deutsch</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
                            href="{{ route('language.swap', 'en') }}">
                            <i class="ti ti-brand-github rounded-circle ti-sm me-2"></i>
                            <span class="align-middle">English</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Language -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if(auth()->check() && auth()->user()->profile_image)
                            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt
                                class="h-auto rounded-circle">
                        @else
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle">
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0">
                    <div class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <div class="avatar avatar-sm me-3">
                                @if(auth()->check() && auth()->user()->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt
                                        class="rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'G' }}
                                    </span>
                                @endif
                            </div>
                            <div class="dropdown-title">
                                @auth
                                    <span class="fw-medium d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                @else
                                    <span class="fw-medium d-block">Gast</span>
                                    <small class="text-muted">Bitte anmelden</small>
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-menu-body">
                        @auth
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="ti ti-user-check me-2 ti-sm"></i>
                                <span class="align-middle">Mein Profil</span>
                            </a>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="ti ti-settings me-2 ti-sm"></i>
                                <span class="align-middle">Einstellungen</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="ti ti-logout me-2 ti-sm"></i>
                                    <span class="align-middle">Abmelden</span>
                                </a>
                            </form>
                        @else
                            <a class="dropdown-item" href="{{ route('login') }}">
                                <i class="ti ti-login me-2 ti-sm"></i>
                                <span class="align-middle">Anmelden</span>
                            </a>
                            <a class="dropdown-item" href="{{ route('register') }}">
                                <i class="ti ti-user-plus me-2 ti-sm"></i>
                                <span class="align-middle">Registrieren</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>