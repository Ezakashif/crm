@props([
    'title',
    'badge' => null,
    'badgeTone' => null, // info|danger
    'actionUrl' => null,
    'actionLabel' => 'View all',
    'padded' => false,
    'id' => null,
])

@php
    $badgeClass = match ($badgeTone) {
        'danger' => 'crm-card__badge--danger',
        'info' => 'crm-card__badge--info',
        default => '',
    };
@endphp

<div @if($id) id="{{ $id }}" @endif {{ $attributes->class(['crm-card']) }}>
    <div class="crm-card__header">
        <h3 class="crm-card__title">{{ $title }}</h3>
        <div class="crm-card__tools">
            @if($badge !== null)
                <span class="crm-card__badge {{ $badgeClass }}">{{ $badge }}</span>
            @endif
            @if($actionUrl)
                <a href="{{ $actionUrl }}" class="crm-card__link">{{ $actionLabel }}</a>
            @endif
            {{ $tools ?? '' }}
        </div>
    </div>
    <div class="crm-card__body {{ $padded ? 'crm-card__body--padded' : '' }}">
        {{ $slot }}
    </div>
</div>
