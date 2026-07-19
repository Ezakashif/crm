@php
    $page = config('marketing.features_page');
    $trialRoute = config('marketing.cta.trial_route', 'register');
    $trialHref = Route::has($trialRoute) ? route($trialRoute) : route('login');
    $demoHref = route(config('marketing.cta.demo_route'), config('marketing.cta.demo_query', []));
@endphp

<x-marketing-layout
    title="Features"
    description="Explore every Algos CRM module—leads, customers, tasks, kanban, reports, permissions, and multi-tenant admin."
    body-class="starter-page-page"
>
    @include('marketing.partials.page-title', [
        'title' => 'Features',
        'subtitle' => $page['headline'],
    ])

    <section class="section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $page['headline'] }}</h2>
            <p>{{ $page['subheadline'] }}</p>
            <div class="mt-4 d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ $trialHref }}" class="btn btn-primary">Start free trial</a>
                <a href="{{ $demoHref }}" class="btn btn-outline-primary">Book demo</a>
            </div>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            @foreach ($page['groups'] as $group)
                <div id="{{ $group['id'] }}" class="mb-5">
                    <div class="section-title text-start" data-aos="fade-up">
                        <h2>{{ $group['title'] }}</h2>
                        <p>{{ $group['description'] }}</p>
                    </div>
                    <div class="row gy-4">
                        @foreach ($group['modules'] as $module)
                            <div class="col-md-6 col-xl-4" data-aos="zoom-in" data-aos-delay="150">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <h3 class="h5">{{ $module['title'] }}</h3>
                                        <p class="text-secondary">{{ $module['description'] }}</p>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($module['highlights'] as $highlight)
                                                <li>{{ $highlight }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="call-to-action section light-background">
        <div class="container text-center" data-aos="fade-up">
            <h2>See Algos in your workflow</h2>
            <p>Start a free trial or book a demo and we’ll walk through the modules that matter to your team.</p>
            <a class="btn btn-primary" href="{{ $trialHref }}">Start free trial</a>
        </div>
    </section>
</x-marketing-layout>
