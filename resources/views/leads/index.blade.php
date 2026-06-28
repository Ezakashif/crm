<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Leads</h1>
            <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Lead
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusColors = [
            'new' => 'card-primary',
            'contacted' => 'card-info',
            'qualified' => 'card-warning',
            'proposal_sent' => 'card-secondary',
            'won' => 'card-success',
            'lost' => 'card-danger',
        ];
    @endphp

    <div class="row">
        @foreach($statuses as $statusKey => $statusTitle)
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card {{ $statusColors[$statusKey] ?? 'card-secondary' }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ $statusTitle }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-light">
                                {{ $leads->where('status', $statusKey)->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body lead-column p-2" data-status="{{ $statusKey }}" style="min-height: 300px;">
                        @foreach($leads->where('status', $statusKey) as $lead)
                            <div class="card card-sm mb-2 lead-card" data-lead-id="{{ $lead->id }}" style="cursor: move;">
                                <div class="card-body p-2">
                                    <h6 class="mb-1">{{ $lead->name }}</h6>
                                    @if($lead->company)
                                        <p class="text-muted small mb-1"><i class="fas fa-building"></i> {{ $lead->company }}</p>
                                    @endif
                                    @if($lead->estimated_value)
                                        <p class="text-muted small mb-1"><i class="fas fa-dollar-sign"></i> {{ number_format($lead->estimated_value, 2) }}</p>
                                    @endif
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-user"></i> {{ optional($lead->assignee)->name ?? 'Unassigned' }}
                                    </div>
                                    <div class="btn-group btn-group-xs">
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-default btn-xs">Edit</a>
                                        @if($lead->status !== 'won')
                                            <form method="POST" action="{{ route('leads.convert', $lead) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-xs">Convert</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.lead-column').forEach(column => {
                    new Sortable(column, {
                        group: 'leads-kanban',
                        animation: 200,
                        draggable: '.lead-card',
                        ghostClass: 'opacity-50',
                        onEnd: function (evt) {
                            fetch("{{ route('leads.board.update') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "Accept": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    lead_id: evt.item.dataset.leadId,
                                    status: evt.to.dataset.status,
                                    sort_order: evt.newIndex + 1
                                })
                            }).then(res => res.json()).then(data => {
                                if (!data.success) alert('Unable to update lead.');
                            }).catch(() => alert('Something went wrong.'));
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
