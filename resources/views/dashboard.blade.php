<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="m-0">Dashboard</h1>
                <small class="text-muted">Here’s what needs your attention today, {{ Auth::user()->name }}.</small>
            </div>
            @if(! empty($quickActions))
                <div class="mt-2 mt-md-0">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['route'] }}" class="btn {{ $action['class'] }} btn-sm mb-1">
                            <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </x-slot>

    {{-- Action KPIs --}}
    <div class="row">
        @if($canViewLeads)
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $todaysFollowUpsCount }}</h3>
                        <p>Today’s Follow-ups</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <a href="#todays-follow-ups" class="small-box-footer">
                        Review list <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        @endif

        @if($canViewTasks)
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingTasksCount }}</h3>
                        <p>Pending Tasks</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="small-box-footer">
                        View tasks <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $overdueTasksCount }}</h3>
                        <p>Overdue Tasks</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="#overdue-tasks" class="small-box-footer">
                        Review list <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        @endif

        @if($canViewLeads)
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($conversionRate, 1) }}<sup style="font-size: 20px">%</sup></h3>
                        <p>Lead Conversion Rate</p>
                    </div>
                    <div class="icon"><i class="fas fa-percentage"></i></div>
                    <a href="{{ route('leads.index') }}" class="small-box-footer">
                        View pipeline <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Lead status KPIs --}}
    @if($canViewLeads)
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-bolt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">New Leads</span>
                        <span class="info-box-number">{{ $newLeadsCount }}</span>
                        <a href="{{ route('leads.index', ['status' => 'new']) }}" class="text-sm">View new leads</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Won Leads</span>
                        <span class="info-box-number">{{ $wonLeadsCount }}</span>
                        <a href="{{ route('leads.index', ['status' => 'won']) }}" class="text-sm">View won leads</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lost Leads</span>
                        <span class="info-box-number">{{ $lostLeadsCount }}</span>
                        <a href="{{ route('leads.index', ['status' => 'lost']) }}" class="text-sm">View lost leads</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Totals --}}
    @if($canViewCustomers || $canViewLeads || $canViewTasks)
        <div class="row">
            @if($canViewCustomers)
                <div class="col-md-4 col-12">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Customers</span>
                            <span class="info-box-number">{{ $customerCount }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if($canViewLeads)
                <div class="col-md-4 col-12">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-funnel-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Leads</span>
                            <span class="info-box-number">{{ $leadCount }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if($canViewTasks)
                <div class="col-md-4 col-12">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tasks"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Tasks</span>
                            <span class="info-box-number">{{ $taskCount }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Charts --}}
    @if($canViewLeads)
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Monthly Lead Growth</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyLeadGrowthChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Lead Source Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="leadSourceChart" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Action lists --}}
    <div class="row">
        @if($canViewLeads)
            <div class="col-lg-6" id="todays-follow-ups">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Today’s Follow-ups</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">{{ $todaysFollowUpsCount }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($todaysFollowUps as $lead)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('leads.show', $lead) }}" class="font-weight-bold">{{ $lead->name }}</a>
                                        <div class="small text-muted">
                                            {{ $lead->statusLabel() }}
                                            @if($lead->assignee)
                                                · {{ $lead->assignee->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge badge-light">{{ optional($lead->follow_up_date)->format('M j') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No follow-ups scheduled for today.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if($canViewTasks)
            <div class="col-lg-6" id="overdue-tasks">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Overdue Tasks</h3>
                        <div class="card-tools">
                            <span class="badge badge-danger">{{ $overdueTasksCount }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($overdueTasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('tasks.edit', $task) }}" class="font-weight-bold">{{ $task->title }}</a>
                                        <div class="small text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            · {{ ucfirst($task->priority) }}
                                            @if($task->assignee)
                                                · {{ $task->assignee->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge badge-danger">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M j') }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No overdue tasks. Nice work.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($canViewTasks)
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Pending Tasks</h3>
                        <div class="card-tools">
                            <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="btn btn-tool">View all</a>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Due</th>
                                    <th>Assignee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tasks.edit', $task) }}">{{ $task->title }}</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->priority === 'urgent' || $task->priority === 'high' ? 'danger' : 'secondary' }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($task->due_date)
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">No pending tasks.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent feeds --}}
    <div class="row">
        @if($canViewLeads)
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Recent Leads</h3>
                        <div class="card-tools">
                            <a href="{{ route('leads.index') }}" class="btn btn-tool btn-sm">View all</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentLeads as $lead)
                                <li class="list-group-item">
                                    <a href="{{ route('leads.show', $lead) }}" class="font-weight-bold">{{ $lead->name }}</a>
                                    <div class="small text-muted">
                                        {{ $lead->statusLabel() }}
                                        · {{ $lead->created_at->diffForHumans() }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No leads yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if($canViewCustomers)
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Recent Customers</h3>
                        <div class="card-tools">
                            <a href="{{ route('customers.index') }}" class="btn btn-tool btn-sm">View all</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentCustomers as $customer)
                                <li class="list-group-item">
                                    <a href="{{ route('customers.edit', $customer) }}" class="font-weight-bold">{{ $customer->name }}</a>
                                    <div class="small text-muted">
                                        {{ $customer->company_name ?? 'No company' }}
                                        · {{ $customer->created_at->diffForHumans() }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No customers yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if($canViewActivityLogs)
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Recent Activities</h3>
                        <div class="card-tools">
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-tool btn-sm">View all</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item">
                                    <div class="font-weight-bold">{{ $activity->actionLabel() }}</div>
                                    <div class="small text-muted">
                                        {{ $activity->description() }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $activity->actor?->name ?? 'System' }}
                                        · {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No recent activity.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($canViewLeads)
        @push('js')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var growthLabels = @json($monthlyLeadGrowth['labels']);
                    var growthData = @json($monthlyLeadGrowth['data']);
                    var sourceLabels = @json($leadSourceDistribution['labels']);
                    var sourceData = @json($leadSourceDistribution['data']);

                    var growthCanvas = document.getElementById('monthlyLeadGrowthChart');
                    if (growthCanvas) {
                        new Chart(growthCanvas.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: growthLabels,
                                datasets: [{
                                    label: 'New leads',
                                    data: growthData,
                                    borderColor: '#007bff',
                                    backgroundColor: 'rgba(0, 123, 255, 0.15)',
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    lineTension: 0.25,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                legend: { display: false },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0
                                        }
                                    }]
                                }
                            }
                        });
                    }

                    var sourceCanvas = document.getElementById('leadSourceChart');
                    if (sourceCanvas) {
                        new Chart(sourceCanvas.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: sourceLabels,
                                datasets: [{
                                    data: sourceData,
                                    backgroundColor: [
                                        '#007bff', '#28a745', '#ffc107', '#17a2b8',
                                        '#6f42c1', '#fd7e14', '#6c757d'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        });
                    }
                });
            </script>
        @endpush
    @endif
</x-app-layout>
