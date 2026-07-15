@props([
    'name',
    'description' => '',
    'monthly' => 0,
    'annual' => 0,
    'features' => [],
    'cta' => 'Start free trial',
    'ctaHref' => null,
    'highlighted' => false,
    'billing' => 'monthly',
])

@php
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $ctaHref = $ctaHref ?? (Route::has($trialRoute) ? route($trialRoute) : route('login'));
    $price = $billing === 'annual' ? $annual : $monthly;
@endphp

<article @class([
    'mk-card relative flex h-full flex-col p-6 sm:p-7',
    'ring-2 ring-sky-500 shadow-lg' => $highlighted,
    'mk-card-interactive' => ! $highlighted,
])>
    @if ($highlighted)
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-sky-600 px-3 py-1 text-xs font-semibold text-white">
            Most popular
        </span>
    @endif

    <div>
        <h3 class="text-xl font-bold tracking-tight text-slate-900">{{ $name }}</h3>
        @if ($description)
            <p class="mt-2 text-sm text-slate-600">{{ $description }}</p>
        @endif
    </div>

    <div class="mt-6 flex items-baseline gap-1">
        <span class="text-4xl font-bold tracking-tight text-slate-900">${{ number_format($price) }}</span>
        <span class="text-sm text-slate-500">/user/mo</span>
    </div>

    <ul class="mt-6 flex-1 space-y-3">
        @foreach ($features as $feature)
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
                <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <x-marketing.icon name="check" size="sm" />
                </span>
                <span>{{ $feature }}</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-8">
        <x-marketing.button
            :href="$ctaHref"
            :variant="$highlighted ? 'primary' : 'secondary'"
            class="w-full"
        >
            {{ $cta }}
        </x-marketing.button>
    </div>
</article>
