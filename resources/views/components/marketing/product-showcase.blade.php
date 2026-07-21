@props([
    'headline' => 'See the CRM in Action',
    'subheadline' => null,
    'items' => [],
    'trialHref' => null,
])

@php
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = $trialHref ?? (Route::has($trialRoute) ? route($trialRoute) : route('login'));
@endphp

<section {{ $attributes->class(['mk-section mk-product-showcase']) }} aria-labelledby="product-showcase-heading">
    <div class="mk-container">
        <div data-mk-reveal>
            <x-marketing.section-heading
                heading-id="product-showcase-heading"
                eyebrow="Product"
                :title="$headline"
                :description="$subheadline"
                align="center"
            />
        </div>

        <div class="mt-4 space-y-16 md:space-y-20 lg:space-y-24">
            @foreach ($items as $index => $item)
                @php
                    $imageLeft = $index % 2 === 0;
                @endphp
                <article
                    id="showcase-{{ $item['id'] }}"
                    class="mk-showcase-row {{ $imageLeft ? 'is-image-left' : 'is-image-right' }}"
                    data-mk-reveal
                    style="--mk-reveal-delay: {{ ($index % 4) * 80 }}ms"
                >
                    <div class="mk-showcase-media">
                        <x-marketing.product-shot
                            :title="$item['title']"
                            :icon="$item['icon'] ?? 'layout-dashboard'"
                            :image="$item['image'] ?? null"
                            :alt="'Algos '.$item['title'].' screenshot'"
                        />
                    </div>

                    <div class="mk-showcase-copy">
                        <p class="mk-showcase-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mk-showcase-title">{{ $item['title'] }}</h3>
                        <p class="mk-showcase-benefit">{{ $item['benefit'] }}</p>
                        <div class="mt-6">
                            <x-marketing.button :href="$trialHref">
                                Start free trial
                                <x-marketing.icon name="arrow-right" size="sm" />
                            </x-marketing.button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
