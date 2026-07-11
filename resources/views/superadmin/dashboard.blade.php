@extends('superadmin.layout')

@section('title', 'Dashboard')
@section('heading', 'Platform overview')
@section('subheading', 'Tenant health across the CRM')

@section('content')
<div class="row">
    @foreach ([
        'companies' => 'Companies',
        'active_companies' => 'Active',
        'suspended_companies' => 'Suspended',
        'users' => 'Tenant users',
        'leads' => 'Leads',
        'customers' => 'Customers',
        'tasks' => 'Tasks',
    ] as $key => $label)
        <div class="col-md-3 col-sm-6">
            <div class="sa-card">
                <div class="sa-muted mb-1">{{ $label }}</div>
                <div class="sa-stat">{{ number_format($stats[$key]) }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="sa-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0 text-white">Recent companies</h2>
        <a href="{{ route('superadmin.companies.create') }}" class="btn btn-sm btn-info">New company</a>
    </div>

    @if ($recentCompanies->isEmpty())
        <p class="sa-muted mb-0">No companies yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recentCompanies as $company)
                    <tr>
                        <td><a href="{{ route('superadmin.companies.show', $company) }}">{{ $company->name }}</a></td>
                        <td class="sa-muted">{{ $company->slug }}</td>
                        <td>
                            <span class="badge badge-{{ $company->status === 'active' ? 'active' : 'suspended' }}">
                                {{ \App\Models\Company::STATUSES[$company->status] ?? ucfirst($company->status) }}
                            </span>
                        </td>
                        <td class="sa-muted">{{ $company->created_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
