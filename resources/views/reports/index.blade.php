<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="m-0">Reports</h1>
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($filters['date_from'])->format('M j, Y') }}
                    –
                    {{ \Carbon\Carbon::parse($filters['date_to'])->format('M j, Y') }}
                </small>
            </div>
        </div>
    </x-slot>

    <x-list-filters :reset-url="route('reports.index')">
        <div class="col-md-2 mb-2">
            <label for="date_from" class="small text-muted mb-1">From</label>
            <input id="date_from" name="date_from" type="date" class="form-control form-control-sm"
                   value="{{ $filters['date_from'] }}">
        </div>
        <div class="col-md-2 mb-2">
            <label for="date_to" class="small text-muted mb-1">To</label>
            <input id="date_to" name="date_to" type="date" class="form-control form-control-sm"
                   value="{{ $filters['date_to'] }}">
        </div>
        @if($canFilterEmployees)
            <div class="col-md-2 mb-2">
                <label for="employee_id" class="small text-muted mb-1">Employee</label>
                <select id="employee_id" name="employee_id" class="form-control form-control-sm">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(($filters['employee_id'] ?? null) == $employee->id)>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if($canViewLeads)
            <div class="col-md-2 mb-2">
                <label for="source" class="small text-muted mb-1">Lead Source</label>
                <select id="source" name="source" class="form-control form-control-sm">
                    <option value="">All sources</option>
                    @foreach($leadSources as $source)
                        <option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>
                            {{ ucfirst(str_replace('_', ' ', $source)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label for="status" class="small text-muted mb-1">Lead Status</label>
                <select id="status" name="status" class="form-control form-control-sm">
                    <option value="">All statuses</option>
                    @foreach($leadStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </x-list-filters>

    @if($canViewLeads && $leads)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Leads</h4>
            @if($canExport)
                <a href="{{ route('reports.export', ['type' => 'leads'] + $filters) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $leads['total'] }}</h3>
                        <p>Total Leads</p>
                    </div>
                    <div class="icon"><i class="fas fa-funnel-dollar"></i></div>
                </div>
            </div>
            @foreach(array_slice($leads['by_status'], 0, 3) as $statusRow)
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-{{ $statusRow['status'] === 'won' ? 'success' : ($statusRow['status'] === 'lost' ? 'danger' : 'secondary') }}">
                            <i class="fas fa-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $statusRow['label'] }}</span>
                            <span class="info-box-number">{{ $statusRow['count'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card card-outline card-primary">
                    <div class="card-header"><h3 class="card-title">Leads by Status</h3></div>
                    <div class="card-body"><canvas id="leadsByStatusChart" height="160"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-outline card-info">
                    <div class="card-header"><h3 class="card-title">Leads by Source</h3></div>
                    <div class="card-body"><canvas id="leadsBySourceChart" height="160"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">Monthly Lead Growth</h3></div>
                    <div class="card-body"><canvas id="monthlyLeadGrowthChart" height="120"></canvas></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Leads by Assigned Employee</h3></div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Leads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads['by_assignee'] as $row)
                                    <tr>
                                        <td>{{ $row['employee'] }}</td>
                                        <td>{{ $row['count'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">No leads in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Leads by Date</h3></div>
            <div class="card-body table-responsive p-0" style="max-height: 260px;">
                <table class="table table-sm table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Leads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads['by_date'] as $day => $count)
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No daily lead data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($canViewCustomers && $customers)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Customers</h4>
            @if($canExport)
                <a href="{{ route('reports.export', ['type' => 'customers'] + $filters) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            @endif
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Customers (range)</span>
                        <span class="info-box-number">{{ $customers['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-user-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">New This Month</span>
                        <span class="info-box-number">{{ $customers['new_this_month'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Converted (Won Leads)</span>
                        <span class="info-box-number">{{ $customers['converted'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Customers by Date</h3></div>
            <div class="card-body table-responsive p-0" style="max-height: 260px;">
                <table class="table table-sm table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers['by_date'] as $row)
                            <tr>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['count'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No customers in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($canViewTasks && $tasks)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Tasks</h4>
            @if($canExport)
                <a href="{{ route('reports.export', ['type' => 'tasks'] + $filters) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            @endif
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $tasks['pending'] }}</h3>
                        <p>Pending Tasks</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $tasks['completed'] }}</h3>
                        <p>Completed Tasks</p>
                    </div>
                    <div class="icon"><i class="fas fa-check"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $tasks['overdue'] }}</h3>
                        <p>Overdue Tasks</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card card-outline card-warning">
                    <div class="card-header"><h3 class="card-title">Tasks by Status</h3></div>
                    <div class="card-body"><canvas id="tasksByStatusChart" height="180"></canvas></div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Tasks by Employee</h3></div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Tasks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks['by_employee'] as $row)
                                    <tr>
                                        <td>{{ $row['employee'] }}</td>
                                        <td>{{ $row['count'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">No tasks in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Tasks by Date</h3></div>
            <div class="card-body table-responsive p-0" style="max-height: 260px;">
                <table class="table table-sm table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks['by_date'] as $row)
                            <tr>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['count'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No daily task data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($canViewLeads && $performance)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Sales Performance</h4>
            @if($canExport)
                <a href="{{ route('reports.export', ['type' => 'performance'] + $filters) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            @endif
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Leads Assigned</span>
                        <span class="info-box-number">{{ $performance['leads_assigned'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success"><i class="fas fa-handshake"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Leads Converted</span>
                        <span class="info-box-number">{{ $performance['leads_converted'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Conversion Rate</span>
                        <span class="info-box-number">{{ number_format($performance['conversion_rate'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Top Performing Employees</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Assigned</th>
                            <th>Converted</th>
                            <th>Conversion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performance['top_performers'] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['employee'] }}</td>
                                <td>{{ $row['assigned'] }}</td>
                                <td>{{ $row['converted'] }}</td>
                                <td>{{ number_format($row['conversion_rate'], 1) }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No performance data in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(! $canViewLeads && ! $canViewCustomers && ! $canViewTasks)
        <div class="alert alert-info mb-0">
            You do not have permission to view lead, customer, or task reports.
        </div>
    @endif

    @push('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function hasChartData(data) {
                    return (data || []).some(function (value) {
                        return Number(value) > 0;
                    });
                }

                function showEmptyChart(canvas) {
                    if (! canvas || ! canvas.parentNode) return;
                    canvas.parentNode.innerHTML = '<p class="text-muted text-center mb-0 py-5">No data for the selected filters.</p>';
                }

                function makeChart(id, type, labels, data, options) {
                    var canvas = document.getElementById(id);
                    if (! canvas || typeof Chart === 'undefined') return;

                    if (! hasChartData(data)) {
                        showEmptyChart(canvas);
                        return;
                    }

                    var chartOptions = {
                        responsive: true,
                        maintainAspectRatio: true,
                        legend: {
                            display: options.showLegend !== false,
                            position: 'bottom'
                        }
                    };

                    if (options.scales) {
                        chartOptions.scales = options.scales;
                    }

                    new Chart(canvas.getContext('2d'), {
                        type: type,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: options.label || '',
                                data: data,
                                backgroundColor: options.backgroundColor || [
                                    '#007bff', '#28a745', '#ffc107', '#17a2b8',
                                    '#6f42c1', '#fd7e14', '#6c757d', '#dc3545'
                                ],
                                borderColor: options.borderColor || '#007bff',
                                borderWidth: options.borderWidth || 1,
                                fill: options.fill || false,
                                lineTension: 0.25
                            }]
                        },
                        options: chartOptions
                    });
                }

                @if($canViewLeads && $leads)
                    makeChart(
                        'leadsByStatusChart',
                        'doughnut',
                        @json($leads['by_status_chart']['labels']),
                        @json($leads['by_status_chart']['data']),
                        { showLegend: true }
                    );

                    makeChart(
                        'leadsBySourceChart',
                        'doughnut',
                        @json($leads['by_source_chart']['labels']),
                        @json($leads['by_source_chart']['data']),
                        { showLegend: true }
                    );

                    makeChart(
                        'monthlyLeadGrowthChart',
                        'line',
                        @json($leads['monthly_growth']['labels']),
                        @json($leads['monthly_growth']['data']),
                        {
                            label: 'Leads',
                            showLegend: false,
                            fill: true,
                            backgroundColor: 'rgba(0, 123, 255, 0.15)',
                            borderColor: '#007bff',
                            borderWidth: 2,
                            scales: {
                                yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                            }
                        }
                    );
                @endif

                @if($canViewTasks && $tasks)
                    makeChart(
                        'tasksByStatusChart',
                        'doughnut',
                        @json($tasks['by_status_chart']['labels']),
                        @json($tasks['by_status_chart']['data']),
                        { showLegend: true }
                    );
                @endif
            });
        </script>
    @endpush
</x-app-layout>
