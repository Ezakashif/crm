@php
    $pricing = config('marketing.pricing');
    $plans = $pricing['plans'] ?? [];
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<x-marketing-layout
    title="Pricing"
    description="Algos CRM pricing for Starter, Professional, and Enterprise—with monthly or annual billing."
    body-class="starter-page-page"
>
    @include('marketing.partials.page-title', [
        'title' => 'Pricing',
        'subtitle' => $pricing['headline'],
    ])

    <section id="pricing" class="pricing section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $pricing['headline'] }}</h2>
            <p>{{ $pricing['subheadline'] }}</p>
            <p class="mt-2"><span class="badge text-bg-primary">{{ $pricing['annual_discount_label'] }}</span> on annual billing</p>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4 justify-content-center">
                @foreach ($plans as $plan)
                    @php
                        $planCtaHref = ($plan['cta_type'] ?? 'trial') === 'demo' ? $demoHref : $trialHref;
                    @endphp
                    <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="150">
                        <div class="pricing-item {{ ! empty($plan['highlighted']) ? 'featured' : '' }} h-100">
                            <h3>{{ $plan['name'] }}</h3>
                            <p>{{ $plan['description'] }}</p>
                            <h4>
                                <sup>$</sup>{{ $plan['monthly'] }}
                                <span> / month</span>
                            </h4>
                            <p class="text-secondary small">or ${{ $plan['annual'] }}/mo billed annually</p>
                            <ul>
                                @foreach ($plan['features'] as $feature)
                                    <li><i class="bi bi-check"></i> <span>{{ $feature }}</span></li>
                                @endforeach
                            </ul>
                            <a href="{{ $planCtaHref }}" class="buy-btn">{{ $plan['cta'] }}</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-secondary mt-5 mb-0">{{ $pricing['future_note'] }}</p>
        </div>
    </section>

    @if (! empty($pricing['faqs']))
        <section id="faq" class="faq section light-background">
            <div class="container section-title" data-aos="fade-up">
                <h2>Pricing FAQ</h2>
            </div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="faq-container">
                            @foreach ($pricing['faqs'] as $index => $faq)
                                <div class="faq-item {{ $index === 0 ? 'faq-active' : '' }}" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                                    <h3>{{ $faq['question'] }}</h3>
                                    <div class="faq-content">
                                        <p>{{ $faq['answer'] }}</p>
                                    </div>
                                    <i class="faq-toggle bi bi-chevron-right"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</x-marketing-layout>
