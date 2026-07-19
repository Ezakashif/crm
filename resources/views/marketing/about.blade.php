@php
    $about = config('marketing.about');
    $brand = config('marketing.name');
@endphp

<x-marketing-layout
    title="About"
    description="Learn about Algos CRM—our mission, vision, story, timeline, and the technology behind the product."
    body-class="starter-page-page"
>
    @include('marketing.partials.page-title', [
        'title' => 'About',
        'subtitle' => $about['headline'],
    ])

    <section class="about section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $about['headline'] }}</h2>
            <p>{{ $about['subheadline'] }}</p>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h3 class="h4">{{ $about['mission']['title'] }}</h3>
                            <p class="mb-0 text-secondary">{{ $about['mission']['body'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h3 class="h4">{{ $about['vision']['title'] }}</h3>
                            <p class="mb-0 text-secondary">{{ $about['vision']['body'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 align-items-start mb-5">
                <div class="col-lg-6">
                    <h3 class="h4">{{ $about['why']['title'] }}</h3>
                    <p class="text-secondary">{{ $about['why']['body'] }}</p>
                </div>
                <div class="col-lg-6">
                    <ul class="list-unstyled mb-0">
                        @foreach ($about['why']['points'] as $point)
                            <li class="d-flex gap-2 mb-3">
                                <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="section-title text-start" data-aos="fade-up">
                <h2>How {{ $brand }} took shape</h2>
            </div>
            <div class="row gy-4">
                @foreach ($about['timeline'] as $item)
                    <div class="col-md-6" data-aos="fade-up">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="text-primary fw-semibold mb-2">{{ $item['year'] ?? ($item['date'] ?? '') }}</div>
                                <h3 class="h5">{{ $item['title'] }}</h3>
                                <p class="mb-0 text-secondary">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-marketing-layout>
