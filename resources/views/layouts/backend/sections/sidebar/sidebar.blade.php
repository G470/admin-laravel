@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/admin') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                @include('_partials.macros',["height"=>40])
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName', 'Inlando') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if(isset($menuData) && isset($menuData[0]) && isset($menuData[0]->menu))
            @foreach ($menuData[0]->menu as $menu)

                {{-- adding active and open class if child is active --}}

                {{-- menu headers --}}
                @if (isset($menu->menuHeader))
                    <li class="menu-header small">
                        <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                    </li>
                @else

                {{-- active menu method --}}
                @php
                $activeClass = null;
                $currentRouteName = Route::currentRouteName() ?? '';

                if ($currentRouteName && $currentRouteName === $menu->slug) {
                    $activeClass = 'active';
                }
                elseif (isset($menu->submenu) && $currentRouteName) {
                    if (gettype($menu->slug) === 'array') {
                        foreach($menu->slug as $slug){
                            if ($slug && str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                $activeClass = 'active open';
                            }
                        }
                    }
                    else{
                        if ($menu->slug && str_contains($currentRouteName, $menu->slug) && strpos($currentRouteName, $menu->slug) === 0) {
                            $activeClass = 'active open';
                        }
                    }
                }
                @endphp

                {{-- main menu --}}
                <li class="menu-item {{$activeClass}}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
                        @isset($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endisset
                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                        @isset($menu->badge)
                            <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                        @endisset
                    </a>

                    {{-- submenu --}}
                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu',['menu' => $menu->submenu])
                    @endisset
                </li>
                @endif
            @endforeach
        @else
            {{-- Fallback if no menu data is available --}}
            <li class="menu-header small">
                <span class="menu-header-text">Menu Loading...</span>
            </li>
        @endif
    </ul>
</aside>
