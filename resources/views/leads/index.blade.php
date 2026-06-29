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

    <x-list-filters :reset-url="route('leads.index')">
        <div class="col-md-3 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Name, email, phone, company..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-2 mb-2">
            <label for="status" class="small text-muted mb-1">Status</label>
            <select id="status" name="status" class="form-control form-control-sm">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <label for="source" class="small text-muted mb-1">Source</label>
            <select id="source" name="source" class="form-control form-control-sm">
                <option value="">All sources</option>
                @foreach(\App\Models\Lead::SOURCES as $source)
                    <option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>
                        {{ ucfirst(str_replace('_', ' ', $source)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label for="assigned_to" class="small text-muted mb-1">Assigned To</label>
            <select id="assigned_to" name="assigned_to" class="form-control form-control-sm">
                <option value="">Anyone</option>
                <option value="unassigned" @selected(($filters['assigned_to'] ?? '') === 'unassigned')>Unassigned</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['assigned_to'] ?? '') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </x-list-filters>

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

    @if($leads->isEmpty())
        <div class="alert alert-info">
            {{ collect($filters ?? [])->filter(fn ($v) => filled($v))->isNotEmpty() ? 'No leads match your filters.' : 'No leads yet.' }}
        </div>
    @endif

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
                                    <h6 class="mb-1">
                                        <a href="{{ route('leads.show', $lead) }}" class="text-dark">{{ $lead->name }}</a>
                                    </h6>
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
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-primary btn-xs">View</a>
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
