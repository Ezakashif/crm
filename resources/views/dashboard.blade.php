<x-app-layout>
    <x-slot name="header">
        <div class="crm-dashboard d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="crm-page-title">Dashboard</h1>
                <span class="crm-page-subtitle">What needs your attention today, {{ Auth::user()->name }}.</span>
            </div>
            @if(! empty($quickActions))
                <div class="crm-header-actions mt-2 mt-md-0 d-flex flex-wrap align-items-center">
                    @foreach($quickActions as $index => $action)
                        <a href="{{ $action['route'] }}"
                           class="btn btn-sm mb-1 {{ $index === 0 ? 'btn-primary' : 'btn-outline-secondary' }} {{ $index > 0 ? 'ml-1' : '' }}">
                            <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/crm-tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <div class="crm-dashboard">
        {{-- Attention strip --}}
        @if($canViewLeads || $canViewTasks)
            <div class="crm-attention">
                @if($canViewLeads)
                    <x-dashboard.kpi-stat
                        label="Follow-ups Today"
                        :value="$todaysFollowUpsCount"
                        href="#todays-follow-ups"
                        meta="Review list"
                        tone="accent"
                    />
                @endif

                @if($canViewTasks)
                    <x-dashboard.kpi-stat
                        label="Pending Tasks"
                        :value="$pendingTasksCount"
                        :href="route('tasks.index', ['status' => 'pending'])"
                        meta="View tasks"
                        tone="warning"
                    />

                    <x-dashboard.kpi-stat
                        label="Overdue Tasks"
                        :value="$overdueTasksCount"
                        href="#overdue-tasks"
                        meta="Review list"
                        tone="danger"
                    />
                @endif

                @if($canViewLeads)
                    <x-dashboard.kpi-stat
                        label="Lead Conversion Rate"
                        :value="number_format($conversionRate, 1).'<sup>%</sup>'"
                        :href="route('leads.index')"
                        meta="View pipeline"
                        tone="success"
                    />
                @endif
            </div>
        @endif

        <div class="row">
            {{-- Work column --}}
            <div class="col-lg-8">
                @if($canViewLeads)
                    <x-dashboard.section-card
                        title="Follow-ups Today"
                        :badge="$todaysFollowUpsCount"
                        badge-tone="info"
                        id="todays-follow-ups"
                    >
                        <ul class="crm-feed">
                            @forelse($todaysFollowUps as $lead)
                                <li class="crm-feed__item">
                                    <div>
                                        <a href="{{ route('leads.show', $lead) }}" class="crm-feed__title">{{ $lead->name }}</a>
                                        <div class="crm-feed__meta">
                                            {{ $lead->statusLabel() }}
                                            @if($lead->assignee)
                                                · {{ $lead->assignee->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="crm-chip">{{ optional($lead->follow_up_date)->format('M j') }}</span>
                                </li>
                            @empty
                                <li>
                                    <x-dashboard.empty-state
                                        icon="fas fa-calendar-check"
                                        title="No follow-ups scheduled for today."
                                        description="You're clear — or schedule a follow-up on a lead."
                                        :action-url="auth()->user()->can('create', App\Models\Lead::class) ? route('leads.create') : null"
                                        :action-label="auth()->user()->can('create', App\Models\Lead::class) ? 'Add Lead' : null"
                                    />
                                </li>
                            @endforelse
                        </ul>
                    </x-dashboard.section-card>
                @endif

                @if($canViewTasks)
                    <x-dashboard.section-card
                        title="Overdue Tasks"
                        :badge="$overdueTasksCount"
                        badge-tone="danger"
                        id="overdue-tasks"
                    >
                        <ul class="crm-feed">
                            @forelse($overdueTasks as $task)
                                <li class="crm-feed__item">
                                    <div>
                                        <a href="{{ route('tasks.edit', $task) }}" class="crm-feed__title">{{ $task->title }}</a>
                                        <div class="crm-feed__meta">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            · {{ ucfirst($task->priority) }}
                                            @if($task->assignee)
                                                · {{ $task->assignee->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="crm-chip crm-chip--danger">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M j') }}
                                    </span>
                                </li>
                            @empty
                                <li>
                                    <x-dashboard.empty-state
                                        icon="fas fa-check-circle"
                                        title="No overdue tasks. Nice work."
                                        description="Keep momentum by clearing pending work next."
                                    />
                                </li>
                            @endforelse
                        </ul>
                    </x-dashboard.section-card>

                    <x-dashboard.section-card
                        title="Pending Tasks"
                        :action-url="route('tasks.index', ['status' => 'pending'])"
                        action-label="View all"
                    >
                        <div class="table-responsive">
                            <table class="table crm-table mb-0">
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
                                                <span class="crm-priority crm-priority--{{ $task->priority }}">
                                                    <span class="crm-priority__dot"></span>
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
                                            <td colspan="4">
                                                <x-dashboard.empty-state
                                                    icon="fas fa-tasks"
                                                    title="No pending tasks."
                                                    description="Create a task when something needs follow-through."
                                                />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-dashboard.section-card>
                @endif
            </div>

            {{-- Insights column --}}
            <div class="col-lg-4">
                @if($canViewLeads)
                    <x-dashboard.section-card title="Lead Pipeline" :padded="true">
                        <div class="crm-insight-grid">
                            <a href="{{ route('leads.index', ['status' => 'new']) }}" class="crm-insight">
                                <div class="crm-insight__label">New Leads</div>
                                <div class="crm-insight__value">{{ $newLeadsCount }}</div>
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'won']) }}" class="crm-insight">
                                <div class="crm-insight__label">Won Leads</div>
                                <div class="crm-insight__value">{{ $wonLeadsCount }}</div>
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'lost']) }}" class="crm-insight">
                                <div class="crm-insight__label">Lost Leads</div>
                                <div class="crm-insight__value">{{ $lostLeadsCount }}</div>
                            </a>
                        </div>
                    </x-dashboard.section-card>
                @endif

                @if($canViewCustomers || $canViewLeads || $canViewTasks)
                    <x-dashboard.section-card title="Workspace Totals" :padded="true">
                        <div class="crm-totals">
                            @if($canViewCustomers)
                                <div class="crm-total-row">
                                    <span class="crm-total-row__label">Customers</span>
                                    <span class="crm-total-row__value">{{ $customerCount }}</span>
                                </div>
                            @endif
                            @if($canViewLeads)
                                <div class="crm-total-row">
                                    <span class="crm-total-row__label">Total Leads</span>
                                    <span class="crm-total-row__value">{{ $leadCount }}</span>
                                </div>
                            @endif
                            @if($canViewTasks)
                                <div class="crm-total-row">
                                    <span class="crm-total-row__label">Total Tasks</span>
                                    <span class="crm-total-row__value">{{ $taskCount }}</span>
                                </div>
                            @endif
                        </div>
                    </x-dashboard.section-card>
                @endif

                @if($canViewLeads)
                    <x-dashboard.section-card title="Lead Source Distribution" :padded="true">
                        <div id="lead-source-shell" class="crm-chart-shell crm-chart-shell--sm is-loading">
                            <div class="crm-chart-skeleton"></div>
                            <canvas id="leadSourceChart"></canvas>
                        </div>
                    </x-dashboard.section-card>
                @endif
            </div>
        </div>

        @if($canViewLeads)
            <x-dashboard.section-card title="Monthly Lead Growth" :padded="true">
                <div id="monthly-lead-growth-shell" class="crm-chart-shell is-loading">
                    <div class="crm-chart-skeleton"></div>
                    <canvas id="monthlyLeadGrowthChart"></canvas>
                </div>
            </x-dashboard.section-card>
        @endif

        {{-- Recent feeds --}}
        <div class="row">
            @if($canViewLeads)
                <div class="col-lg-4">
                    <x-dashboard.section-card
                        title="Recent Leads"
                        :action-url="route('leads.index')"
                    >
                        <ul class="crm-feed">
                            @forelse($recentLeads as $lead)
                                <li class="crm-feed__item">
                                    <div>
                                        <a href="{{ route('leads.show', $lead) }}" class="crm-feed__title">{{ $lead->name }}</a>
                                        <div class="crm-feed__meta">
                                            {{ $lead->statusLabel() }}
                                            · {{ $lead->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li>
                                    <x-dashboard.empty-state
                                        icon="fas fa-user-plus"
                                        title="No leads yet."
                                        description="Add your first lead to start the pipeline."
                                    />
                                </li>
                            @endforelse
                        </ul>
                    </x-dashboard.section-card>
                </div>
            @endif

            @if($canViewCustomers)
                <div class="col-lg-4">
                    <x-dashboard.section-card
                        title="Recent Customers"
                        :action-url="route('customers.index')"
                    >
                        <ul class="crm-feed">
                            @forelse($recentCustomers as $customer)
                                <li class="crm-feed__item">
                                    <div>
                                        <a href="{{ route('customers.show', $customer) }}" class="crm-feed__title">{{ $customer->name }}</a>
                                        <div class="crm-feed__meta">
                                            {{ $customer->company_name ?? 'No company' }}
                                            · {{ $customer->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li>
                                    <x-dashboard.empty-state
                                        icon="fas fa-building"
                                        title="No customers yet."
                                        description="Convert a won lead or add a customer."
                                    />
                                </li>
                            @endforelse
                        </ul>
                    </x-dashboard.section-card>
                </div>
            @endif

            @if($canViewActivityLogs)
                <div class="col-lg-4">
                    <x-dashboard.section-card
                        title="Recent Activities"
                        :action-url="route('activity-logs.index')"
                    >
                        <ul class="crm-feed">
                            @forelse($recentActivities as $activity)
                                @php $subjectUrl = $activity->subjectShowUrl(); @endphp
                                <li class="crm-feed__item">
                                    <div>
                                        <div class="crm-feed__title">{{ $activity->actionLabel() }}</div>
                                        <div class="crm-feed__meta">
                                            @if($subjectUrl)
                                                <a href="{{ $subjectUrl }}">{{ $activity->description() }}</a>
                                            @else
                                                {{ $activity->description() }}
                                            @endif
                                        </div>
                                        <div class="crm-feed__meta">
                                            {{ $activity->actor?->name ?? 'System' }}
                                            · {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li>
                                    <x-dashboard.empty-state
                                        icon="fas fa-stream"
                                        title="No recent activity."
                                        description="CRM actions will show up here as your team works."
                                    />
                                </li>
                            @endforelse
                        </ul>
                    </x-dashboard.section-card>
                </div>
            @endif
        </div>
    </div>

    @if($canViewLeads)
        @push('js')
            @include('partials.dashboard.chart-scripts')
        @endpush
    @endif
</x-app-layout>
