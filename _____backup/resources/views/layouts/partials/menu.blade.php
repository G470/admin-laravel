<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/logo.png') }}" alt="Inlando Logo" height="30">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">Inlando</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Benutzer -->
        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Benutzer</div>
            </a>
        </li>

        <!-- Vermietungsobjekte -->
        <li class="menu-item {{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
            <a href="{{ route('admin.rentals.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building-store"></i>
                <div>Vermietungsobjekte</div>
            </a>
        </li>

        <!-- Kategorien -->
        <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <a href="{{ route('admin.categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-category"></i>
                <div>Kategorien</div>
            </a>
        </li>

        <!-- Dynamic Rental Fields -->
        <li class="menu-item {{ request()->routeIs('admin.rental-field-templates.*') ? 'active' : '' }}">
            <a href="{{ route('admin.rental-field-templates.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-template"></i>
                <div>Dynamic Rental Fields</div>
            </a>
        </li>

        <!-- Städte SEO -->
        <li class="menu-item {{ request()->routeIs('admin.cities-seo.*') ? 'active' : '' }}">
            <a href="{{ route('admin.cities-seo.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-map"></i>
                <div>Städte SEO</div>
            </a>
        </li>

        <!-- Formulare -->
        <li class="menu-item {{ request()->routeIs('admin.forms.*') ? 'active' : '' }}">
            <a href="{{ route('admin.forms.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div>Formulare</div>
            </a>
        </li>

        <!-- E-Mail-Vorlagen -->
        <li class="menu-item {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
            <a href="{{ route('admin.email-templates.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-mail"></i>
                <div>E-Mail-Vorlagen</div>
            </a>
        </li>

        <!-- Rechnungen -->
        <li class="menu-item {{ request()->routeIs('admin.bills.*') ? 'active' : '' }}">
            <a href="{{ route('admin.bills.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-receipt"></i>
                <div>Rechnungen</div>
            </a>
        </li>

        <!-- Badwords -->
        <li class="menu-item {{ request()->routeIs('admin.badwords.*') ? 'active' : '' }}">
            <a href="{{ route('admin.badwords.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-ban"></i>
                <div>Badwords</div>
            </a>
        </li>

        <!-- Einstellungen -->
        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div>Einstellungen</div>
            </a>
        </li>

        <!-- Rollenverwaltung -->
        <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-lock"></i>
                <div>Rollenverwaltung</div>
            </a>
        </li>

        <!-- Permissionsverwaltung -->
        <li class="menu-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
            <a href="{{ route('admin.permissions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-key"></i>
                <div>Permissionsverwaltung</div>
            </a>
        </li>
    </ul>
</aside>