@php
    /** @var \Illuminate\Support\Collection<int, \App\Support\CustomerTimelineEvent> $timeline */
    $timeline = $timeline ?? collect();
@endphp

@push('css')
<style>
    .customer-timeline {
        position: relative;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .customer-timeline::before {
        content: '';
        position: absolute;
        top: 0.75rem;
        bottom: 0.75rem;
        left: 1.1rem;
        width: 2px;
        background: linear-gradient(180deg, #ced4da 0%, #e9ecef 100%);
    }
    .customer-timeline-item {
        position: relative;
        display: flex;
        align-items: flex-start;
        padding-bottom: 1.15rem;
    }
    .customer-timeline-item:last-child {
        padding-bottom: 0;
    }
    .customer-timeline-marker {
        position: relative;
        z-index: 1;
        flex: 0 0 2.2rem;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.8rem;
        box-shadow: 0 0 0 4px #fff;
        margin-right: 0.85rem;
    }
    .customer-timeline-body {
        flex: 1 1 auto;
        min-width: 0;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.4rem;
        padding: 0.7rem 0.85rem;
    }
    a.customer-timeline-link {
        color: inherit;
        text-decoration: none;
        display: block;
        flex: 1 1 auto;
        min-width: 0;
    }
    a.customer-timeline-link:hover .customer-timeline-body {
        border-color: #adb5bd;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">Customer Timeline</h3>
        <div class="card-tools">
            <span class="badge badge-light">{{ $timeline->count() }}</span>
        </div>
    </div>
    <div class="card-body">
        @if($timeline->isEmpty())
            <p class="text-muted text-center mb-0">
                No timeline history yet. Convert a lead or add related tasks to build this customer’s story.
            </p>
        @else
            <ul class="customer-timeline">
                @foreach($timeline as $event)
                    <li class="customer-timeline-item">
                        <span class="customer-timeline-marker bg-{{ $event->color }}">
                            <i class="{{ $event->icon }}"></i>
                        </span>

                        @if($event->url)
                            <a href="{{ $event->url }}" class="customer-timeline-link">
                                @include('customers.partials.timeline-event-body', ['event' => $event])
                            </a>
                        @else
                            @include('customers.partials.timeline-event-body', ['event' => $event])
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
