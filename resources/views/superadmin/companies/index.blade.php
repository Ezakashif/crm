@extends('superadmin.layout')

@section('title', 'Companies')
@section('heading', 'Companies')
@section('subheading', 'Manage tenant organizations across the platform')

@section('content')
@if (session('import_errors'))
    <div class="alert alert-warning">
        <strong>Import notes:</strong>
        <ul class="mb-0 pl-3">
            @foreach (session('import_errors') as $error)
                <li>Row {{ $error['row'] }}: {{ $error['message'] }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="sa-card">
    <form method="GET" class="form-row align-items-end">
        <div class="form-group col-md-3 mb-md-0">
            <label class="sa-muted">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Name, slug, email, owner">
        </div>
        <div class="form-group col-md-2 mb-md-0">
            <label class="sa-muted">Status</label>
            <select name="status" class="custom-select">
                <option value="">All</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2 mb-md-0">
            <label class="sa-muted">Subscription</label>
            <select name="subscription_status" class="custom-select">
                <option value="">All</option>
                @foreach ($subscriptionStatuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['subscription_status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2 mb-md-0">
            <label class="sa-muted">Plan</label>
            <select name="plan_id" class="custom-select">
                <option value="">All</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" @selected((string) ($filters['plan_id'] ?? '') === (string) $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3 mb-0 d-flex flex-wrap">
            <button class="btn btn-outline-light mr-2 mb-2">Filter</button>
            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-info mr-2 mb-2">New company</a>
            <a href="{{ route('superadmin.companies.import.create') }}" class="btn btn-outline-light mr-2 mb-2">Import CSV</a>
            <a href="{{ route('superadmin.companies.export', request()->query()) }}" class="btn btn-outline-light mr-2 mb-2">Export CSV</a>
            <a href="{{ route('superadmin.companies.export.pdf', request()->query()) }}" class="btn btn-outline-light mb-2">Export PDF</a>
        </div>
    </form>
</div>

<div class="sa-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
            <tr>
                <th>Company Name</th>
                <th>Owner</th>
                <th>Plan</th>
                <th>Users</th>
                <th>Leads</th>
                <th>Customers</th>
                <th>Status</th>
                <th>Last Active</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td>
                        <div class="font-weight-bold text-white">{{ $company->name }}</div>
                        <div class="sa-muted small">{{ $company->slug }}</div>
                    </td>
                    <td>
                        <div class="text-white">{{ $company->owner?->name ?? '—' }}</div>
                        <div class="sa-muted small">{{ $company->owner?->email ?? $company->email ?? '—' }}</div>
                    </td>
                    <td class="sa-muted">{{ $company->plan?->name ?? '—' }}</td>
                    <td>{{ $company->users_count }}</td>
                    <td>{{ $company->leads_count }}</td>
                    <td>{{ $company->customers_count }}</td>
                    <td>
                        <span class="badge badge-{{ $company->status === 'active' ? 'active' : 'suspended' }}">
                            {{ $statuses[$company->status] ?? ucfirst($company->status) }}
                        </span>
                        <div class="sa-muted small mt-1">{{ $subscriptionStatuses[$company->subscription_status] ?? ucfirst((string) $company->subscription_status) }}</div>
                    </td>
                    <td class="sa-muted">{{ $company->last_active_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="sa-muted">{{ $company->created_at?->format('Y-m-d') }}</td>
                    <td class="btn-group-actions text-nowrap">
                        <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-sm btn-outline-light" title="View">View</a>
                        <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-sm btn-outline-light" title="Edit">Edit</a>
                        @if ($company->status === 'active')
                            <form method="POST" action="{{ route('superadmin.companies.status', $company) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="suspended">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Suspend this company?')">Suspend</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('superadmin.companies.status', $company) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <button class="btn btn-sm btn-outline-success">Activate</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('superadmin.companies.impersonate', $company) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-info" onclick="return confirm('Login as this company admin?')">Login as</button>
                        </form>
                        @if ($company->slug !== \App\Models\Company::DEFAULT_SLUG)
                            <form method="POST" action="{{ route('superadmin.companies.destroy', $company) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Soft-delete this company?')">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="sa-muted">No companies found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $companies->links() }}
    </div>
</div>
@endsection
