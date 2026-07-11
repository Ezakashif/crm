@extends('superadmin.layout')

@section('title', 'Companies')
@section('heading', 'Companies')
@section('subheading', 'Manage tenant organizations')

@section('content')
<div class="sa-card">
    <form method="GET" class="form-row align-items-end">
        <div class="form-group col-md-5 mb-md-0">
            <label class="sa-muted">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Name or slug">
        </div>
        <div class="form-group col-md-3 mb-md-0">
            <label class="sa-muted">Status</label>
            <select name="status" class="custom-select">
                <option value="">All</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4 mb-0 d-flex">
            <button class="btn btn-outline-light mr-2">Filter</button>
            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-info">New company</a>
        </div>
    </form>
</div>

<div class="sa-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
            <tr>
                <th>Company</th>
                <th>Status</th>
                <th>Users</th>
                <th>Leads</th>
                <th>Customers</th>
                <th>Tasks</th>
                <th></th>
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
                        <span class="badge badge-{{ $company->status === 'active' ? 'active' : 'suspended' }}">
                            {{ $statuses[$company->status] ?? ucfirst($company->status) }}
                        </span>
                    </td>
                    <td>{{ $company->users_count }}</td>
                    <td>{{ $company->leads_count }}</td>
                    <td>{{ $company->customers_count }}</td>
                    <td>{{ $company->tasks_count }}</td>
                    <td class="text-right">
                        <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-sm btn-outline-light">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="sa-muted">No companies found.</td>
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
