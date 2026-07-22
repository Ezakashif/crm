@php
    $page = config('marketing.features_page');
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<x-marketing-layout
    title="Features"
    description="Explore every Algos CRM module—leads, customers, tasks, kanban, reports, permissions, and multi-tenant admin."
>
    {{-- Hero --}}
    <section class="mk-atmosphere">
        <div class="mk-container mk-section pb-12 md:pb-16">
            <div class="mk-hero-copy mx-auto max-w-3xl text-center">
                <p class="mk-brand-hero mk-brand-hero-page mb-5" aria-label="{{ config('marketing.name') }}">
                    {{ strtolower(config('marketing.name')) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display mk-page-title">
                    {{ $page['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $page['subheadline'] }}
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-marketing.button :href="$trialHref" size="lg">
                        <x-marketing.trial-cta-label />
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                    <x-marketing.button :href="$demoHref" variant="secondary" size="lg">
                        Book demo
                    </x-marketing.button>
                </div>
            </div>
        </div>
    </section>

    {{-- Module groups --}}
    @foreach ($page['groups'] as $index => $group)
        <section
            id="{{ $group['id'] }}"
            class="mk-section {{ $index % 2 === 1 ? 'mk-section-muted' : 'bg-white' }}"
            aria-labelledby="group-{{ $group['id'] }}"
        >
            <div class="mk-container">
                <div data-mk-reveal>
                    <x-marketing.section-heading
                        :heading-id="'group-'.$group['id']"
                        eyebrow="Modules"
                        :title="$group['title']"
                        :description="$group['description']"
                    />
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($group['modules'] as $moduleIndex => $module)
                        <div data-mk-reveal style="--mk-reveal-delay: {{ ($moduleIndex + 1) * 100 }}ms">
                            <x-marketing.feature-module
                                :icon="$module['icon']"
                                :title="$module['title']"
                                :description="$module['description']"
                                :highlights="$module['highlights']"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    {{-- Closing CTA --}}
    <x-marketing.cta
        title="See Algos in your workflow"
        description="Start a free trial or book a demo and we’ll walk through the modules that matter to your team."
    />
</x-marketing-layout>
