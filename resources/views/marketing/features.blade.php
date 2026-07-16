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
            <div class="mk-fade-up mx-auto max-w-3xl text-center">
                <p class="mk-brand-hero mb-5 text-[2.5rem] sm:text-5xl" aria-label="{{ config('marketing.name') }}">
                    {{ strtolower(config('marketing.name')) }}<span class="dot">.</span>
                </p>
                <h1 class="mk-display text-3xl sm:text-4xl lg:text-5xl">
                    {{ $page['headline'] }}
                </h1>
                <p class="mk-lead mx-auto mt-5 max-w-2xl">
                    {{ $page['subheadline'] }}
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
    </section>

    {{-- Module groups --}}
    @foreach ($page['groups'] as $index => $group)
        <section
            id="{{ $group['id'] }}"
            class="mk-section {{ $index % 2 === 1 ? 'mk-section-muted' : 'bg-white' }}"
            aria-labelledby="group-{{ $group['id'] }}"
        >
            <div class="mk-container">
                <x-marketing.section-heading
                    :heading-id="'group-'.$group['id']"
                    eyebrow="Modules"
                    :title="$group['title']"
                    :description="$group['description']"
                    class="mb-10"
                />

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($group['modules'] as $module)
                        <x-marketing.feature-module
                            :icon="$module['icon']"
                            :title="$module['title']"
                            :description="$module['description']"
                            :highlights="$module['highlights']"
                        />
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
