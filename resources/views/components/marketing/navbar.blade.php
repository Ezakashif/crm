@php
    $navItems = config('marketing.nav', []);
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $demoRoute = config('marketing.cta.demo_route', 'marketing.contact');
    $demoQuery = config('marketing.cta.demo_query', []);
@endphp

<header class="mk-nav" x-data="marketingNav" @keydown.escape.window="close()">
    <div class="mk-container flex h-full items-center justify-between gap-4">
        <div class="flex items-center gap-8">
            <x-marketing.logo />

            <nav class="hidden items-center gap-6 lg:flex" aria-label="Primary">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="mk-nav-link {{ $active ? 'is-active' : '' }}"
                        @if ($active) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <x-marketing.button
                    href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}"
                    variant="secondary"
                    size="sm"
                >
                    Go to app
                </x-marketing.button>
            @else
                <x-marketing.button href="{{ route('login') }}" variant="ghost" size="sm">
                    Log in
                </x-marketing.button>
                <x-marketing.button href="{{ route($demoRoute, $demoQuery) }}" variant="secondary" size="sm">
                    Book demo
                </x-marketing.button>
                <x-marketing.button href="{{ Route::has($trialRoute) ? route($trialRoute) : route('login') }}" size="sm">
                    Start free trial
                </x-marketing.button>
            @endauth
        </div>

        <button
            type="button"
            class="mk-btn mk-btn-ghost mk-btn-sm lg:hidden"
            @click="toggle()"
            :aria-expanded="open.toString()"
            aria-controls="mobile-nav"
            aria-label="Toggle navigation"
        >
            <x-marketing.icon name="menu" x-show="!open" />
            <x-marketing.icon name="x" x-cloak x-show="open" />
        </button>
    </div>

    <div
        id="mobile-nav"
        x-cloak
        x-show="open"
        x-transition.opacity
        class="border-t border-slate-200 bg-white lg:hidden"
        @click.outside="close()"
    >
        <nav class="mk-container flex flex-col gap-1 py-4" aria-label="Mobile">
            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="rounded-lg px-3 py-3 text-base font-medium text-slate-700 hover:bg-slate-50 {{ $active ? 'bg-slate-50 text-slate-900' : '' }}"
                    @if ($active) aria-current="page" @endif
                    @click="close()"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="mt-3 flex flex-col gap-2 border-t border-slate-100 pt-4">
                @auth
                    <x-marketing.button
                        href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}"
                        variant="secondary"
                        class="w-full"
                    >
                        Go to app
                    </x-marketing.button>
                @else
                    <x-marketing.button href="{{ route('login') }}" variant="ghost" class="w-full">
                        Log in
                    </x-marketing.button>
                    <x-marketing.button href="{{ route($demoRoute, $demoQuery) }}" variant="secondary" class="w-full">
                        Book demo
                    </x-marketing.button>
                    <x-marketing.button href="{{ Route::has($trialRoute) ? route($trialRoute) : route('login') }}" class="w-full">
                        Start free trial
                    </x-marketing.button>
                @endauth
            </div>
        </nav>
    </div>
</header>
