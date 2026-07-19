@props([
    'title',
    'subtitle' => null,
])

<div class="page-title light-background">
    <div class="container">
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p class="mb-0 mt-2 text-secondary">{{ $subtitle }}</p>
        @endif
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ route('marketing.home') }}">Home</a></li>
                <li class="current">{{ $title }}</li>
            </ol>
        </nav>
    </div>
</div>
