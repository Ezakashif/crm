<div {{ $attributes->class(['card border-0 shadow-sm']) }} role="img" aria-label="Map placeholder for {{ $address ?? config('marketing.contact.address') }}">
    <div class="card-body text-center py-5">
        <i class="bi bi-geo-alt-fill text-primary fs-2 d-block mb-3"></i>
        <p class="fw-semibold mb-1">Google Maps placeholder</p>
        <p class="text-secondary mb-0">{{ $address ?? config('marketing.contact.address') }}</p>
    </div>
</div>
