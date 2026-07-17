@php
    $brand = config('marketing.name');
    $home = config('marketing.home');
    $plans = config('marketing.pricing.plans', []);
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<x-marketing-layout
    :title="null"
    :description="config('marketing.description')"
>
    {{-- Hero --}}
    <section class="mk-atmosphere mk-hero">
        <div class="mk-container">
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
            </div>
        </div>

        <div class="mk-hero-preview mk-hero-preview-bleed">
            <div class="mk-float-soft">
                <x-marketing.dashboard-preview />
            </div>
        </div>
    </section>

    {{-- Trusted by --}}
    <section class="border-y border-slate-200/80 bg-white py-10" aria-labelledby="trusted-by-heading">
        <div class="mk-container">
            <h2
                id="trusted-by-heading"
                class="text-center text-sm font-semibold uppercase tracking-wide text-slate-500"
                data-mk-reveal
            >
                Trusted by growing revenue teams
            </h2>
            <div class="mk-logo-row mt-6">
                @foreach ($home['trusted_by'] as $index => $logo)
                    <div
                        class="mk-logo-mark"
                        data-mk-reveal
                        style="--mk-reveal-delay: {{ $index * 60 }}ms"
                        aria-hidden="true"
                    >{{ $logo }}</div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features overview --}}
    <section class="mk-section" aria-labelledby="features-heading">
        <div class="mk-container">
            <div data-mk-reveal>
                <x-marketing.section-heading
                    heading-id="features-heading"
                    eyebrow="Features"
                    title="Everything your pipeline needs"
                    description="From first lead to closed customer—modules that keep sales and ops aligned."
                    align="center"
                    class="mb-10"
                />
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($home['features'] as $index => $feature)
                    <div data-mk-reveal style="--mk-reveal-delay: {{ $index * 70 }}ms">
                        <x-marketing.feature-card
                            :icon="$feature['icon']"
                            :title="$feature['title']"
                            :description="$feature['description']"
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
                    class="mb-10"
                />
            </div>
            <ol class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($home['how_it_works'] as $index => $step)
                    <li class="mk-step" data-mk-reveal style="--mk-reveal-delay: {{ $index * 80 }}ms">
                        <div class="mk-step-num">{{ $step['step'] }}</div>
                        <h3 class="mt-3 text-lg font-semibold tracking-tight text-slate-900">{{ $step['title'] }}</h3>
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
            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                @foreach ($home['why_us'] as $index => $item)
                    <div
                        class="flex gap-4 rounded-xl border border-slate-200 bg-slate-50/80 p-5"
                        data-mk-reveal
                        style="--mk-reveal-delay: {{ $index * 90 }}ms"
                    >
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
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
    <section class="mk-section border-y border-slate-200 bg-slate-900" aria-labelledby="stats-heading" data-mk-reveal>
        <div class="mk-container">
            <h2 id="stats-heading" class="sr-only">Algos at a glance</h2>
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach ($home['stats'] as $index => $stat)
                    <div class="text-center lg:text-left" style="--mk-reveal-delay: {{ $index * 70 }}ms">
                        <div class="mk-stat-value text-white">{{ $stat['value'] }}</div>
                        <div class="mt-2 text-sm font-medium text-slate-400">{{ $stat['label'] }}</div>
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
                    description="Placeholder stories from revenue teams using a clearer CRM workflow."
                    align="center"
                    class="mb-10"
                />
            </div>
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ($home['testimonials'] as $index => $item)
                    <div data-mk-reveal style="--mk-reveal-delay: {{ $index * 90 }}ms">
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
                    description="Simple monthly or annual pricing. Full comparison on the pricing page."
                    align="center"
                />
            </div>

            <div class="mt-8 flex justify-center" data-mk-reveal>
                <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-1" role="group" aria-label="Billing period">
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
                    <div data-mk-reveal style="--mk-reveal-delay: {{ $index * 90 }}ms">
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
                    Compare plans
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
            <div data-mk-reveal style="--mk-reveal-delay: 100ms">
                <x-marketing.faq-accordion :items="$home['faqs']" open="trial" />
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <x-marketing.cta
        title="Ready to run your pipeline in Algos?"
        description="Start a free trial today, or book a demo and we’ll walk you through the workspace."
    />
</x-marketing-layout>
