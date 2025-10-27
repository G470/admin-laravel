<!-- Navbar: Start -->
<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-4">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 py-lg-2 me-4">
        <!-- Mobile menu toggle: Start-->
        <button class="navbar-toggler border-0 px-0 me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="ti ti-menu-2 ti-lg align-middle"></i>
        </button>
        <!-- Mobile menu toggle: End-->
        <a href="{{ route('home') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="{{ asset('assets/img/branding/inlando-logo.svg') }}" alt="Inlando Logo" class="navbar-brand-logo" style="height: 40px; width: auto;">
          </span>
        </a>
      </div>
      <!-- Menu logo wrapper: End -->
      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="ti ti-x ti-lg"></i>
        </button>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link fw-medium" href="{{ route('home') }}">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="{{ route('home') }}#kategorien">Kategorien</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="{{ route('how-it-works') }}">Wie es funktioniert</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="{{ route('about') }}">Über uns</a>
          </li>
        </ul>
      </div>
      <!-- Menu wrapper: End -->
      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-link fw-medium" href="{{ route('rent-out') }}">Jetzt vermieten</a>
        </li>
        @guest
          <li class="nav-item me-2 ">
            <a class="btn btn-outline-primary waves-effect" href="{{ route('login') }}">Anmelden</a>
          </li>
          <li class="nav-item ps-2 ">
            <a class="btn btn-primary waves-effect waves-light" href="{{ route('register') }}">Registrieren</a>
          </li>
          <!-- Guest user favorites page buttun with heart icon -->
           <li class="nav-item">
            <a class="nav-link" href="{{ route('guestuser.favorites') }}">
              <i class="ti ti-heart ti-lg text-primary"></i>
            </a>
          </li>

        @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-medium" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              {{ Auth::user()->name }}
              @if(Auth::user()->is_admin)
                <span class="badge bg-danger ms-1">Admin</span>
              @elseif(Auth::user()->is_vendor)
                <span class="badge bg-success ms-1">Vendor</span>
              @else
                <span class="badge bg-primary ms-1">User</span>
              @endif
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="ti ti-user me-2"></i>Mein Profil</a></li>
              @if(Auth::user()->is_vendor || Auth::user()->is_admin)              <li><a class="dropdown-item" href="{{ route('vendor-dashboard') }}"><i class="ti ti-dashboard me-2"></i>Vendor Dashboard</a></li>
              <li><a class="dropdown-item" href="#"><i class="ti ti-box me-2"></i>Meine Artikel</a></li>
              @endif
              <li><a class="dropdown-item" href="{{ route('user.bookings') }}"><i class="ti ti-calendar me-2"></i>Meine Buchungen</a></li>
              <li><a class="dropdown-item" href="{{ route('user.favorites') }}"><i class="ti ti-heart me-2"></i>Favoriten</a></li>
              @if(Auth::user()->is_admin)
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="ti ti-settings me-2"></i>Admin Panel</a></li>
              @endif
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('user.settings') }}"><i class="ti ti-settings me-2"></i>Einstellungen</a></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Möchten Sie sich wirklich abmelden?')">
                    <i class="ti ti-logout me-2"></i>Abmelden
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @endguest
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
<!-- Navbar: End -->
