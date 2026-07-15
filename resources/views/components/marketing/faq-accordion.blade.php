@props([
    'items' => [],
    'open' => null,
])

<div
    {{ $attributes->class(['mk-card p-2 sm:p-4']) }}
    x-data="faqAccordion(@js($open))"
>
    @foreach ($items as $index => $item)
        <x-marketing.faq-item
            :id="$item['id'] ?? ('faq-'.$index)"
            :question="$item['question']"
        >
            {{ $item['answer'] }}
        </x-marketing.faq-item>
    @endforeach

    {{ $slot }}
</div>
