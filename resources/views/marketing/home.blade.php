@php
    $brand = config('marketing.name');
    $home = config('marketing.home');
    $plans = config('marketing.pricing.plans', []);
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
    $showcase = $home['product_showcase'] ?? [];
@endphp

<x-marketing-layout
    :title="null"
    :description="config('marketing.description')"
>
    {{-- Hero --}}
    <section class="mk-atmosphere mk-hero">
        <div class="mk-hero-shapes absolute inset-0" aria-hidden="true">
            <span class="mk-hero-shape mk-hero-shape-1"></span>
            <span class="mk-hero-shape mk-hero-shape-2"></span>
        </div>
        <div class="mk-container relative">
            <div class="mk-hero-copy mx-auto max-w-3xl text-center">
                <p class="mk-brand-hero mb-6" aria-label="{{ $brand }}">
                    {{ strtolower($brand) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display text-3xl sm:text-4xl lg:text-5xl">
                    {{ $home['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $home['subheadline'] }}
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-marketing.button :href="$trialHref" size="lg">
                        Start free trial
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                    <x-marketing.button :href="$demoHref" variant="secondary" size="lg">
                        Book demo
                    </x-marketing.button>
                </div>
                <p class="mt-3 text-sm text-slate-500">No credit card required</p>
                <div class="mt-6 flex justify-center" data-mk-reveal>
                    <x-marketing.trust-chips :items="$home['trust_chips'] ?? []" />
                </div>
            </div>
        </div>

        <div class="mk-hero-preview mk-hero-preview-bleed mk-hero-preview-lg relative">
            <div class="mk-float-soft">
                <x-marketing.dashboard-preview class="mk-dashboard-preview-lg" />
            </div>
        </div>
    </section>

    {{-- Trusted by --}}
    <section class="border-y border-slate-200/70 bg-white py-8" aria-labelledby="trusted-by-heading">
        <div class="mk-container">
            <h2
                id="trusted-by-heading"
                class="text-center text-xs font-semibold uppercase tracking-[0.14em] text-slate-400"
                data-mk-reveal
            >
                Trusted by growing revenue teams
            </h2>
            <div class="mk-logo-row mt-6">
                @foreach ($home['trusted_by'] as $index => $logo)
                    <div
                        class="mk-logo-mark"
                        data-mk-reveal
                        style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms"
                        aria-hidden="true"
                    >{{ $logo }}</div>
                @endforeach
            </div>
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

    {{-- Trust --}}
    <section class="mk-section bg-white" aria-labelledby="trust-heading">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="trust-heading"
                    eyebrow="Trust"
                    title="Built for secure, multi-tenant CRM operations"
                    description="Replace these badges with customer logos and testimonials when you are ready—without changing the layout."
                    align="center"
                />
            </div>
            <x-marketing.trust-badges :items="$home['trust_badges'] ?? []" class="mt-10" />
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

    {{-- Statistics --}}
    <section class="mk-stats-band" aria-labelledby="stats-heading">
        <div class="mk-container relative">
            <h2 id="stats-heading" class="sr-only">Algos at a glance</h2>
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach ($home['stats'] as $index => $stat)
                    <div
                        class="mk-stat-item text-center lg:text-left"
                        data-mk-reveal
                        style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms"
                    >
                        @if (! empty($stat['count']))
                            <div
                                class="mk-stat-value"
                                data-mk-counter
                                data-mk-target="{{ $stat['count'] }}"
                                data-mk-suffix="{{ $stat['suffix'] ?? '' }}"
                                data-mk-prefix="{{ $stat['prefix'] ?? '' }}"
                            >{{ $stat['value'] }}</div>
                        @else
                            <div class="mk-stat-value">{{ $stat['value'] }}</div>
                        @endif
                        <div class="mk-stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mk-section" aria-labelledby="testimonials-heading">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="testimonials-heading"
                    eyebrow="Customers"
                    title="Teams that switched to Algos"
                    description="Early stories from revenue teams using a clearer CRM workflow. Swap in real quotes anytime."
                    align="center"
                />
            </div>
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ($home['testimonials'] as $index => $item)
                    <div data-mk-reveal style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms">
                        <x-marketing.testimonial-card
                            :quote="$item['quote']"
                            :name="$item['name']"
                            :role="$item['role']"
                            :company="$item['company']"
                        />
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
