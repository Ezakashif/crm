@props([
    'title' => 'Ready to organize your sales pipeline?',
    'description' => 'Start your free trial today. No credit card required.',
    'note' => 'No credit card required',
    'primaryLabel' => 'Start free trial',
    'primaryHref' => null,
    'secondaryLabel' => 'Book demo',
    'secondaryHref' => null,
])

@php
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $demoRoute = config('marketing.cta.demo_route', 'marketing.contact');
    $demoQuery = config('marketing.cta.demo_query', []);

    $primaryHref = $primaryHref ?? (Route::has($trialRoute) ? route($trialRoute) : route('login'));
    $secondaryHref = $secondaryHref ?? route($demoRoute, $demoQuery);
@endphp

<section {{ $attributes->class(['mk-section']) }}>
    <div class="mk-container">
        <div
            class="relative overflow-hidden px-6 py-12 text-center sm:px-12 sm:py-16"
            style="border-radius: var(--mk-radius-xl); background: #0f172a;"
            data-mk-reveal="zoom"
        >
            <div
                class="pointer-events-none absolute inset-0 opacity-80"
                aria-hidden="true"
                style="background: radial-gradient(500px 220px at 20% 0%, rgba(56,189,248,0.28), transparent 60%), radial-gradient(420px 200px at 90% 100%, rgba(2,132,199,0.22), transparent 55%);"
            ></div>

            <div class="relative mx-auto max-w-2xl">
                <h2 class="mk-display text-3xl text-white sm:text-4xl">{{ $title }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-slate-300">
                    {{ $description }}
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-marketing.button :href="$primaryHref" size="lg">
                        {{ $primaryLabel }}
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                    <x-marketing.button :href="$secondaryHref" variant="on-dark" size="lg">
                        {{ $secondaryLabel }}
                    </x-marketing.button>
                </div>
                @if ($note)
                    <p class="mt-4 text-sm font-medium text-slate-400">{{ $note }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
