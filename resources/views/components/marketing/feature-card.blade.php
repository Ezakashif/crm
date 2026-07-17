@props([
    'title',
    'description',
    'icon' => 'sparkles',
])

<article {{ $attributes->class(['mk-card mk-card-interactive p-6']) }}>
    <div class="mk-icon-well mb-4 h-11 w-11">
        <x-marketing.icon :name="$icon" />
    </div>
    <h3 class="text-lg font-semibold tracking-tight text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
</article>
