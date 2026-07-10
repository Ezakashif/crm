<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="crm-page-title">Search</h1>
                @if($term !== '')
                    <span class="crm-page-subtitle">Results for “{{ $term }}”</span>
                @else
                    <span class="crm-page-subtitle">Find leads, customers, tasks, users, and companies</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card card-outline card-secondary mb-3 crm-filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('search.index') }}" class="form-inline flex-wrap">
                <div class="input-group input-group-sm mr-2 mb-2" style="min-width: 280px; max-width: 480px; width: 100%;">
                    <input type="search" name="q" value="{{ $term }}"
                           class="form-control js-global-search"
                           placeholder="Name, email, phone, company, task, user..."
                           minlength="{{ \App\Services\GlobalSearchService::MIN_TERM_LENGTH }}"
                           maxlength="100"
                           autocomplete="off"
                           autofocus>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($term === '')
        <div class="crm-empty mb-0">
            <i class="fas fa-search"></i>
            Type at least {{ \App\Services\GlobalSearchService::MIN_TERM_LENGTH }} characters to search leads, customers, tasks, users, and companies.
        </div>
    @elseif($too_short)
        <div class="alert alert-warning mb-0">
            Enter at least {{ \App\Services\GlobalSearchService::MIN_TERM_LENGTH }} characters to search.
        </div>
    @elseif($total === 0)
        <div class="crm-empty mb-0">
            <i class="fas fa-search"></i>
            No matches found for “{{ $term }}”.
        </div>
    @else
        <p class="text-muted mb-3">
            Showing up to {{ \App\Services\GlobalSearchService::PER_CATEGORY_LIMIT }} results per category
            ({{ $total }} shown).
        </p>

        @if($can_view_leads)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-funnel-dollar text-info mr-1"></i>
                        Leads
                        <span class="badge badge-info">{{ $leads->count() }}</span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                <tr>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->email ?? '—' }}</td>
                                    <td>{{ $lead->phone ?? '—' }}</td>
                                    <td>{{ $lead->company ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $lead->statusLabel() }}</span>
                                    </td>
                                    <td class="text-right">
                                        @can('view', $lead)
                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-xs btn-outline-primary">
                                                View
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">No matching leads.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($can_view_customers)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users text-success mr-1"></i>
                        Customers
                        <span class="badge badge-success">{{ $customers->count() }}</span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email ?? '—' }}</td>
                                    <td>{{ $customer->phone ?? '—' }}</td>
                                    <td>{{ $customer->company_name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($customer->status) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @can('view', $customer)
                                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-xs btn-outline-primary">
                                                View
                                            </a>
                                        @endcan
                                        @can('update', $customer)
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-xs btn-outline-secondary">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">No matching customers.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($can_view_users)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-shield text-dark mr-1"></i>
                        Users
                        <span class="badge badge-dark">{{ $users->count() }}</span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $resultUser)
                                <tr>
                                    <td>{{ $resultUser->name }}</td>
                                    <td>{{ $resultUser->email }}</td>
                                    <td>{{ $resultUser->roleNames() ?: '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $resultUser->statusBadgeClass() }}">
                                            {{ ucfirst($resultUser->status) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @can('view', $resultUser)
                                            <a href="{{ route('users.show', $resultUser) }}" class="btn btn-xs btn-outline-primary">
                                                View
                                            </a>
                                        @endcan
                                        @can('update', $resultUser)
                                            <a href="{{ route('users.edit', $resultUser) }}" class="btn btn-xs btn-outline-secondary">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">No matching users.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($can_view_tasks)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tasks text-primary mr-1"></i>
                        Tasks
                        <span class="badge badge-primary">{{ $tasks->count() }}</span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Assignee</th>
                                <th>Related</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>
                                        <div>{{ $task->title }}</div>
                                        @if(filled($task->description))
                                            <small class="text-muted">
                                                {{ \Illuminate\Support\Str::limit($task->description, 80) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($task->priority) }}</td>
                                    <td>{{ $task->assignee?->name ?? '—' }}</td>
                                    <td>{{ $task->customer?->name ?? $task->lead?->name ?? '—' }}</td>
                                    <td class="text-right">
                                        @can('view', $task)
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-xs btn-outline-primary">
                                                View
                                            </a>
                                        @endcan
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-xs btn-outline-secondary">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">No matching tasks.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($can_view_leads || $can_view_customers)
            <div class="card mb-0">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-building text-warning mr-1"></i>
                        Companies
                        <span class="badge badge-warning">{{ count($companies) }}</span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Found in</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company['name'] }}</td>
                                    <td>
                                        @foreach($company['sources'] as $source)
                                            <span class="badge badge-light border">{{ ucfirst($source) }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @if(in_array('leads', $company['sources'], true))
                                            <a href="{{ route('leads.index', ['search' => $company['name']]) }}"
                                               class="btn btn-xs btn-outline-info">
                                                Leads
                                            </a>
                                        @endif
                                        @if(in_array('customers', $company['sources'], true))
                                            <a href="{{ route('customers.index', ['search' => $company['name']]) }}"
                                               class="btn btn-xs btn-outline-success">
                                                Customers
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">No matching companies.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
