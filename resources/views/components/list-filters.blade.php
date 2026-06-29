@props(['resetUrl' => null])

@php
    $hasFilters = collect(request()->query())
        ->except('page')
        ->filter(fn ($value) => filled($value))
        ->isNotEmpty();
@endphp

<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ url()->current() }}">
            <div class="row align-items-end">
                {{ $slot }}
                <div class="col-md-auto mb-2">
                    <button type="submit" class="btn btn-primary btn-sm mr-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    @if($hasFilters)
                        <a href="{{ $resetUrl ?? url()->current() }}" class="btn btn-default btn-sm">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
