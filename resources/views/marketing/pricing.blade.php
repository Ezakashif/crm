@php
    $pricing = config('marketing.pricing');
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<x-marketing-layout
    title="Pricing"
    description="Algos CRM pricing for Starter, Professional, and Enterprise—with monthly or annual billing."
>
    {{-- Hero --}}
    <section class="mk-atmosphere">
        <div class="mk-container mk-section pb-10 md:pb-12">
            <div class="mk-hero-copy mx-auto max-w-3xl text-center">
                <p class="mk-brand-hero mk-brand-hero-page mb-5" aria-label="{{ config('marketing.name') }}">
                    {{ strtolower(config('marketing.name')) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display mk-page-title">
                    {{ $pricing['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $pricing['subheadline'] }}
                </p>
                <p class="mt-3 text-sm font-medium text-slate-500">
                    {{ $pricing['trial_note'] ?? 'No credit card required · Cancel anytime during trial' }}
                </p>
            </div>
        </div>
    </section>

    {{-- Plans --}}
    <section class="bg-white pb-16 pt-4 md:pb-20" aria-labelledby="plans-heading" x-data="pricingToggle('monthly')">
        <div class="mk-container">
            <h2 id="plans-heading" class="sr-only">Pricing plans</h2>

            <div class="flex justify-center" data-mk-reveal>
                <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1 shadow-sm" role="group" aria-label="Billing period">
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
                            {{ $pricing['annual_discount_label'] }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @forelse ($plans as $index => $plan)
                    @php
                        $planCtaHref = $plan->is_free ? $trialHref : $demoHref;
                        $features = $plan->features->map(fn ($feature) => $feature->feature_value ?: $feature->feature_name)
                            ->concat($plan->limits->map(fn ($limit) => $limit->limit_name.': '.($limit->isUnlimited() ? 'Unlimited' : trim($limit->limit_value.' '.$limit->unit))))->all();
                    @endphp
                    <div data-mk-reveal style="--mk-reveal-delay: {{ ($index + 1) * 100 }}ms">
                        <div x-show="!isAnnual()">
                            <x-marketing.pricing-card
                                :name="$plan->name"
                                :description="$plan->short_description ?: $plan->description"
                                :monthly="$plan->monthly_price"
                                :annual="$plan->yearly_price"
                                :features="$features"
                                :cta="$plan->is_free ? 'Start Free Trial' : 'Contact Sales'"
                                :cta-type="$plan->is_free ? 'trial' : 'demo'"
                                :highlighted="$plan->is_featured"
                                :cta-href="$planCtaHref"
                                :currency="$plan->currency"
                                :trial-days="$plan->trial_days"
                                billing="monthly"
                            />
                        </div>
                        <div x-cloak x-show="isAnnual()">
                            <x-marketing.pricing-card
                                :name="$plan->name"
                                :description="$plan->short_description ?: $plan->description"
                                :monthly="$plan->monthly_price"
                                :annual="$plan->yearly_price"
                                :features="$features"
                                :cta="$plan->is_free ? 'Start Free Trial' : 'Contact Sales'"
                                :cta-type="$plan->is_free ? 'trial' : 'demo'"
                                :highlighted="$plan->is_featured"
                                :cta-href="$planCtaHref"
                                :currency="$plan->currency"
                                :trial-days="$plan->trial_days"
                                billing="annual"
                            />
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-slate-500">Pricing is being finalized. Contact us to discuss your workspace.</p>
                @endforelse
            </div>

            <p class="mt-8 text-center text-sm text-slate-500">
                {{ $pricing['future_note'] }}
            </p>
        </div>
    </section>

    {{-- Comparison --}}
    <section class="mk-section mk-section-muted" aria-labelledby="compare-heading">
        <div class="mk-container">
            <x-marketing.section-heading
                heading-id="compare-heading"
                eyebrow="Compare"
                title="Feature comparison"
                description="See what’s included in Starter, Professional, and Enterprise at a glance."
                align="center"
            />

            <x-marketing.pricing-comparison
                :rows="$comparisonRows"
                :plans="$plans"
            />
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mk-section bg-white" aria-labelledby="pricing-faq-heading">
        <div class="mk-container grid gap-10 lg:grid-cols-[1fr_1.15fr] lg:items-start">
            <x-marketing.section-heading
                heading-id="pricing-faq-heading"
                eyebrow="FAQ"
                title="Pricing questions"
                description="Billing, trials, and what to expect as you get started."
            />
            <x-marketing.faq-accordion :items="$pricing['faqs']" open="billing" />
        </div>
    </section>

    {{-- CTA --}}
    <x-marketing.cta
        title="Ready to organize your sales pipeline?"
        description="Start your free trial today. No credit card required—or talk with us about Enterprise."
        note="No credit card required"
    />
</x-marketing-layout>
