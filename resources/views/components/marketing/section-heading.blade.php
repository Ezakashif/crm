@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'align' => 'left',
])

@php
    $alignClass = $align === 'center' ? 'text-center mx-auto' : 'text-left';
@endphp

<div {{ $attributes->class(['max-w-2xl', $alignClass]) }}>
    @if ($eyebrow)
        <p class="mk-eyebrow mb-3">{{ $eyebrow }}</p>
    @endif

    <h2 class="mk-display text-3xl sm:text-4xl">{{ $title ?? $slot }}</h2>

    @if ($description)
        <p class="mk-lead mt-4">{{ $description }}</p>
    @endif
</div>
