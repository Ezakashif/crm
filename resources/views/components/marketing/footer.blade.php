@php
    $brand = config('marketing.name');
    $contact = config('marketing.contact');
    $social = config('marketing.social');
    $navItems = config('marketing.nav', []);
@endphp

<footer class="border-t border-slate-200 bg-slate-900 text-slate-300">
    <div class="mk-container py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <a href="{{ route('marketing.home') }}" class="inline-flex items-center gap-2.5 no-underline">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-sky-400" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 4 L5 20" />
                            <path d="M12 4 L19 20" />
                            <path d="M8.5 14.5 H15.5" />
                        </svg>
                    </span>
                    <span class="text-xl font-bold tracking-tight text-white">
                        {{ strtolower($brand) }}<span class="text-sky-400">.</span>
                    </span>
                </a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-400">
                    {{ config('marketing.tagline') }}
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Product</h2>
                <ul class="mt-4 space-y-2.5">
                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="text-sm text-slate-400 transition hover:text-white">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Company</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                    <li class="flex items-start gap-2">
                        <x-marketing.icon name="mail" size="sm" class="mt-0.5 text-slate-500" />
                        <a href="mailto:{{ $contact['email'] }}" class="hover:text-white">{{ $contact['email'] }}</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-marketing.icon name="phone" size="sm" class="mt-0.5 text-slate-500" />
                        <span>{{ $contact['phone'] }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-marketing.icon name="map-pin" size="sm" class="mt-0.5 text-slate-500" />
                        <span>{{ $contact['address'] }}</span>
                    </li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Follow</h2>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ $social['linkedin'] }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800 text-slate-300 transition hover:bg-slate-700 hover:text-white" aria-label="LinkedIn">
                        <x-marketing.icon name="linkedin" />
                    </a>
                    <a href="{{ $social['twitter'] }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800 text-slate-300 transition hover:bg-slate-700 hover:text-white" aria-label="X / Twitter">
                        <x-marketing.icon name="twitter" />
                    </a>
                    <a href="{{ $social['github'] }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800 text-slate-300 transition hover:bg-slate-700 hover:text-white" aria-label="GitHub">
                        <x-marketing.icon name="github" />
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-slate-800 pt-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</p>
            <div class="flex gap-4">
                <span class="cursor-default">Privacy</span>
                <span class="cursor-default">Terms</span>
            </div>
        </div>
    </div>
</footer>
