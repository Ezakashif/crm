@php
    $brand = config('marketing.name');
    $home = config('marketing.home');
    $plans = config('marketing.pricing.plans', []);
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
    $showcase = $home['product_showcase'] ?? [];
    $trialDays = $trialDays ?? 14;
    $trialDurationLabel = $trialDays.'-day free trial';
    $trustChips = [...($home['trust_chips'] ?? []), $trialDurationLabel];
@endphp

<x-marketing-layout
    :title="null"
    :description="config('marketing.description')"
>
    {{-- Hero --}}
    <section class="mk-atmosphere mk-hero mk-hero-refined">
        <div class="mk-hero-shapes absolute inset-0" aria-hidden="true">
            <span class="mk-hero-shape mk-hero-shape-1"></span>
            <span class="mk-hero-shape mk-hero-shape-2"></span>
        </div>
        <div class="mk-container relative">
            <div class="mk-hero-copy mx-auto max-w-4xl text-center">
                <p class="mk-brand-hero mb-6" aria-label="{{ $brand }}">
                    {{ strtolower($brand) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display text-3xl sm:text-4xl lg:text-5xl">
                    {{ $home['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $home['subheadline'] }}
                </p>
                <div class="mk-hero-actions mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-marketing.button :href="$trialHref" size="lg">
                        Start free trial
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                    <x-marketing.button :href="$demoHref" variant="secondary" size="lg">
                        Book demo
                    </x-marketing.button>
                </div>
                <p class="mk-hero-reassurance mt-3 text-sm text-slate-500">No credit card required</p>
                <div class="mk-hero-trust mt-7" data-mk-reveal aria-label="Platform capabilities">
                    <x-marketing.trust-chips :items="$trustChips" />
                </div>
            </div>
        </div>

        <div class="mk-hero-preview mk-hero-preview-bleed mk-hero-preview-lg relative">
            <div class="mk-hero-preview-halo" aria-hidden="true"></div>
            <div class="mk-hero-product-label">
                <span class="mk-hero-product-status" aria-hidden="true"></span>
                CRM workspace
            </div>
            <div class="mk-float-soft relative">
                <figure class="mk-dashboard-preview mk-dashboard-preview-lg">
                    <img
                        src="{{ asset('marketing/screenshots/overview.PNG') }}"
                        alt="Algos CRM dashboard showing revenue overview, pipeline stages, and today's tasks"
                        class="mk-dashboard-preview-image"
                        fetchpriority="high"
                        decoding="async"
                    >
                </figure>
            </div>
        </div>
    </section>

    {{-- Trust --}}
    <section class="mk-trust-strip" aria-labelledby="trust-heading">
        <div class="mk-container mk-trust-strip-layout">
            <div class="mk-trust-strip-intro" data-mk-reveal="left">
                <p class="mk-eyebrow">Built for confident operations</p>
                <h2 id="trust-heading">The CRM foundation your team can rely on</h2>
                <p>Clear ownership, controlled access, and the visibility to keep customer work moving.</p>
                <div class="mt-6">
                    <x-marketing.button :href="$trialHref">
                        Start {{ $trialDurationLabel }}
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                </div>
            </div>
            <x-marketing.trust-badges :items="$home['trust_badges'] ?? []" class="mk-trust-strip-badges" />
        </div>
    </section>

    {{-- Product showcase --}}
    @if (! empty($showcase['items']))
        <x-marketing.product-showcase
            :headline="$showcase['headline'] ?? 'See the CRM in Action'"
            :subheadline="$showcase['subheadline'] ?? null"
            :items="$showcase['items']"
            :trial-href="$trialHref"
            class="bg-white"
        />
    @endif

    {{-- Features overview --}}
    <section class="mk-section mk-section-muted" aria-labelledby="features-heading">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="features-heading"
                    eyebrow="Features"
                    title="Outcomes for every stage of the pipeline"
                    description="Each module solves a real sales-ops problem—and turns it into measurable team leverage."
                    align="center"
                />
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($home['features'] as $index => $feature)
                    <div data-mk-reveal style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms">
                        <x-marketing.feature-card
                            :icon="$feature['icon']"
                            :title="$feature['title']"
                            :problem="$feature['problem'] ?? null"
                            :solution="$feature['solution'] ?? null"
                            :benefit="$feature['benefit'] ?? null"
                            :description="$feature['description'] ?? null"
                        />
                    </div>
                @endforeach
            </div>
            <div class="mt-10 text-center" data-mk-reveal>
                <x-marketing.button href="{{ route('marketing.features') }}" variant="secondary">
                    Explore all features
                    <x-marketing.icon name="arrow-right" size="sm" />
                </x-marketing.button>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="mk-section mk-section-muted" aria-labelledby="how-heading">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="how-heading"
                    eyebrow="How it works"
                    title="Up and running in four steps"
                    description="A simple path from empty workspace to a team that never drops a follow-up."
                    align="center"
                />
            </div>
            <ol class="mk-steps grid gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach ($home['how_it_works'] as $index => $step)
                    <li class="mk-step" data-mk-reveal style="--mk-reveal-delay: {{ $index * 100 }}ms">
                        <div class="mk-step-num">{{ $step['step'] }}</div>
                        <h3 class="mt-4 text-lg font-semibold tracking-tight text-slate-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['description'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Why choose us --}}
    <section class="mk-section bg-white" aria-labelledby="why-heading">
        <div class="mk-container grid gap-10 lg:grid-cols-[1fr_1.2fr] lg:items-center">
            <div data-mk-reveal="left">
                <x-marketing.section-heading
                    heading-id="why-heading"
                    eyebrow="Why Algos"
                    title="Built for teams that outgrow spreadsheets"
                    description="Modern CRM structure without the noise—so your team spends time selling, not configuring."
                />
            </div>
            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                @foreach ($home['why_us'] as $index => $item)
                    <div
                        class="mk-panel flex gap-4 p-5"
                        data-mk-reveal="right"
                        style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms"
                    >
                        <span class="mk-icon-well h-10 w-10 shrink-0">
                            <x-marketing.icon :name="$item['icon']" />
                        </span>
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $item['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing preview --}}
    <section class="mk-section mk-section-muted" aria-labelledby="pricing-heading" x-data="pricingToggle('monthly')">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="pricing-heading"
                    eyebrow="Pricing"
                    title="Plans that scale with your team"
                    description="Simple monthly or annual pricing. Professional is recommended for most growing sales teams."
                    align="center"
                />
            </div>

            <p class="mt-2 text-center text-sm font-medium text-slate-500" data-mk-reveal>
                {{ config('marketing.pricing.trial_note') }}
            </p>

            <div class="mt-6 flex justify-center" data-mk-reveal>
                <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm" role="group" aria-label="Billing period">
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="!isAnnual() ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500'"
                        @click="setBilling('monthly')"
                    >
                        Monthly
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="isAnnual() ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500'"
                        @click="setBilling('annual')"
                    >
                        Annual
                        <span class="ml-1 text-xs font-medium text-sky-600" :class="isAnnual() ? '!text-sky-300' : ''">
                            {{ config('marketing.pricing.annual_discount_label') }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($plans as $index => $plan)
                    @php
                        $planCtaHref = $plan['id'] === 'enterprise' ? $demoHref : $trialHref;
                    @endphp
                    <div data-mk-reveal style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms">
                        <div x-show="!isAnnual()">
                            <x-marketing.pricing-card
                                :name="$plan['name']"
                                :description="$plan['description']"
                                :monthly="$plan['monthly']"
                                :annual="$plan['annual']"
                                :features="$plan['features']"
                                :cta="$plan['cta']"
                                :highlighted="$plan['highlighted']"
                                :cta-href="$planCtaHref"
                                billing="monthly"
                            />
                        </div>
                        <div x-cloak x-show="isAnnual()">
                            <x-marketing.pricing-card
                                :name="$plan['name']"
                                :description="$plan['description']"
                                :monthly="$plan['monthly']"
                                :annual="$plan['annual']"
                                :features="$plan['features']"
                                :cta="$plan['cta']"
                                :highlighted="$plan['highlighted']"
                                :cta-href="$planCtaHref"
                                billing="annual"
                            />
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center" data-mk-reveal>
                <x-marketing.button href="{{ route('marketing.pricing') }}" variant="secondary">
                    Compare all features
                    <x-marketing.icon name="arrow-right" size="sm" />
                </x-marketing.button>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mk-section bg-white" aria-labelledby="faq-heading">
        <div class="mk-container grid gap-10 lg:grid-cols-[1fr_1.15fr] lg:items-start">
            <div data-mk-reveal="left">
                <x-marketing.section-heading
                    heading-id="faq-heading"
                    eyebrow="FAQ"
                    title="Questions, answered"
                    description="Quick answers about trials, data, and how Algos fits your team."
                />
            </div>
            <div data-mk-reveal="right" style="--mk-reveal-delay: 200ms">
                <x-marketing.faq-accordion :items="$home['faqs']" open="trial" />
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <x-marketing.cta
        title="Ready to organize your sales pipeline?"
        description="Start your free trial today. No credit card required—or book a demo and we’ll walk you through the workspace."
        note="No credit card required"
    />
</x-marketing-layout>
