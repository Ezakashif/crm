@php
    $brand = config('marketing.name');
    $navItems = config('marketing.nav', []);
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="{{ route('marketing.home') }}" class="logo d-flex align-items-center me-auto me-xl-0">
            <img src="{{ asset('marketing/assets/img/logo.webp') }}" alt="{{ $brand }}">
            <h1 class="sitename">{{ $brand }}</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li>
                    <a href="{{ route('marketing.home') }}" class="{{ request()->routeIs('marketing.home') ? 'active' : '' }}">
                        Home
                    </a>
                </li>
                @foreach ($navItems as $item)
                    <li>
                        <a
                            href="{{ route($item['route']) }}"
                            class="{{ request()->routeIs($item['route']) ? 'active' : '' }}"
                            @if (request()->routeIs($item['route'])) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        @auth
            <a
                class="btn-getstarted"
                href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}"
            >
                Go to app
            </a>
        @else
            <a class="btn-getstarted" href="{{ $trialHref }}">Get Started</a>
        @endauth
    </div>
</header>
