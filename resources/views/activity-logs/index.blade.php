<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="crm-page-title">{{ $canViewAll ? 'Activity Log' : 'My Activity' }}</h1>
            <span class="crm-page-subtitle">
                {{ $canViewAll ? 'Audit trail across leads, customers, tasks, and users.' : 'Your recent actions in the CRM.' }}
            </span>
        </div>
    </x-slot>

    @if($canViewAll)
        <div class="card card-outline card-secondary mb-3 crm-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('activity-logs.index') }}" class="form-inline">
                    <div class="form-group mr-2 mb-2">
                        <label for="user_id" class="mr-2">User</label>
                        <select name="user_id" id="user_id" class="form-control form-control-sm">
                            <option value="">All users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="action" class="mr-2">Action</label>
                        <select name="action" id="action" class="form-control form-control-sm">
                            <option value="">All actions</option>
                            @foreach($actions as $value => $label)
                                <option value="{{ $value }}" @selected(request('action') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mb-2 mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-default mb-2">Clear</a>
                </form>
            </div>
        </div>
    @else
        <div class="card card-outline card-secondary mb-3 crm-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('activity-logs.index') }}" class="form-inline">
                    <div class="form-group mr-2 mb-2">
                        <label for="action" class="mr-2">Action</label>
                        <select name="action" id="action" class="form-control form-control-sm">
                            <option value="">All actions</option>
                            @foreach($actions as $value => $label)
                                <option value="{{ $value }}" @selected(request('action') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mb-2 mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-default mb-2">Clear</a>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 160px">Date</th>
                        <th style="width: 180px">User</th>
                        <th style="width: 160px">Action</th>
                        <th>Details</th>
                        <th style="width: 120px">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php $subjectUrl = $log->subjectShowUrl(); @endphp
                        <tr @if($subjectUrl) class="activity-log-row" style="cursor: pointer;" data-href="{{ $subjectUrl }}" @endif>
                            <td>
                                <small>{{ $log->created_at->format('M d, Y') }}<br>
                                {{ $log->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                @if($log->actor)
                                    <div class="d-flex align-items-center">
                                        <x-user-avatar :user="$log->actor" :size="28" class="mr-2" />
                                        <span>{{ $log->actor->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $log->actionLabel() }}</span>
                            </td>
                            <td>
                                @if($subjectUrl)
                                    <a href="{{ $subjectUrl }}" class="text-dark">
                                        {{ $log->description() }}
                                        <i class="fas fa-external-link-alt text-muted ml-1" style="font-size: 0.7rem;"></i>
                                    </a>
                                @else
                                    {{ $log->description() }}
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $log->ip_address ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer clearfix">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('tr.activity-log-row[data-href]').forEach(function (row) {
                    row.addEventListener('click', function (event) {
                        if (event.target.closest('a')) {
                            return;
                        }

                        window.location = row.getAttribute('data-href');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
