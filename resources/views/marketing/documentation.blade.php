@php
    $page = config('marketing.documentation');
    $brand = config('marketing.name');
    $trialAvailable = \App\Support\MarketingCta::trialAvailable();
    $trialHref = \App\Support\MarketingCta::trialHref();
    $demoHref = \App\Support\MarketingCta::demoHref();
@endphp

<x-marketing-layout
    title="Documentation"
    description="Algos CRM documentation—getting started, trials, data import, multi-tenant workspaces, permissions, and in-app product guides."
>
    {{-- Hero --}}
    <section class="mk-atmosphere">
        <div class="mk-container mk-section pb-12 md:pb-16">
            <div class="mk-hero-copy mx-auto max-w-3xl text-center">
                <p class="mk-brand-hero mk-brand-hero-page mb-5" aria-label="{{ $brand }}">
                    {{ strtolower($brand) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display mk-page-title">
                    {{ $page['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $page['subheadline'] }}
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    @if ($trialAvailable)
                        <x-marketing.button :href="$trialHref" size="lg">
                            <x-marketing.trial-cta-label />
                            <x-marketing.icon name="arrow-right" size="sm" />
                        </x-marketing.button>
                        <x-marketing.button :href="$demoHref" variant="secondary" size="lg">
                            Book demo
                        </x-marketing.button>
                    @else
                        <x-marketing.button :href="$demoHref" size="lg">
                            Book demo
                            <x-marketing.icon name="arrow-right" size="sm" />
                        </x-marketing.button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Topic index --}}
    <section class="mk-section bg-white border-b border-slate-200/80" aria-labelledby="docs-topics-heading">
        <div class="mk-container">
            <h2 id="docs-topics-heading" class="sr-only">Documentation topics</h2>
            <nav aria-label="Documentation topics">
                <ul class="flex flex-wrap gap-2 justify-center">
                    @foreach ($page['sections'] as $section)
                        <li>
                            <a
                                href="#{{ $section['id'] }}"
                                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-sm font-medium text-slate-700 transition hover:border-sky-300 hover:text-sky-800"
                            >
                                {{ $section['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </section>

    {{-- Sections --}}
    @foreach ($page['sections'] as $index => $section)
        <section
            id="{{ $section['id'] }}"
            class="mk-section {{ $index % 2 === 1 ? 'mk-section-muted' : 'bg-white' }}"
            aria-labelledby="docs-{{ $section['id'] }}"
        >
            <div class="mk-container grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
                <div data-mk-reveal>
                    <x-marketing.section-heading
                        :heading-id="'docs-'.$section['id']"
                        eyebrow="Guide"
                        :title="$section['title']"
                        :description="$section['body']"
                    />
                </div>
                <ul class="space-y-3" data-mk-reveal="right" style="--mk-reveal-delay: 150ms">
                    @foreach ($section['points'] as $point)
                        <li class="flex items-start gap-3 border-b border-slate-200/80 px-1 py-3.5 text-sm font-medium text-slate-800 last:border-b-0">
                            <span class="mk-icon-well mt-0.5 h-5 w-5 shrink-0 text-emerald-700" style="background: #ecfdf5;">
                                <x-marketing.icon name="check" size="sm" />
                            </span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endforeach

    {{-- Related links --}}
    <section class="mk-section bg-white" aria-labelledby="docs-related-heading">
        <div class="mk-container">
            <x-marketing.section-heading
                heading-id="docs-related-heading"
                eyebrow="Next steps"
                title="Keep exploring"
                description="Jump into product details, pricing, or talk with the team when you need a hand."
                align="center"
            />
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row" data-mk-reveal>
                <x-marketing.button :href="route('marketing.features')" variant="secondary">
                    Features
                </x-marketing.button>
                <x-marketing.button :href="route('marketing.pricing')" variant="secondary">
                    Pricing
                </x-marketing.button>
                <x-marketing.button :href="route('marketing.contact', ['intent' => 'support'])" variant="secondary">
                    Contact support
                </x-marketing.button>
                @auth
                    <x-marketing.button :href="route('docs.index')">
                        Open in-app docs
                    </x-marketing.button>
                @else
                    <x-marketing.button :href="route('login')">
                        Log in for full docs
                    </x-marketing.button>
                @endauth
            </div>
        </div>
    </section>

    <x-marketing.cta title="Ready to organize your sales pipeline?" />
</x-marketing-layout>
