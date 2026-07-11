@extends('superadmin.layout')

@section('title', 'Search')
@section('heading', 'Global search')
@section('subheading', 'Find companies and users across the platform')

@section('content')
<div class="sa-card">
    <form method="GET" class="form-row align-items-end">
        <div class="form-group col-md-8 mb-md-0">
            <label class="sa-muted">Query</label>
            <input type="search" name="q" value="{{ $term }}" class="form-control" placeholder="Company, slug, email, owner...">
        </div>
        <div class="form-group col-md-4 mb-0">
            <button class="btn btn-info">Search</button>
        </div>
    </form>
</div>

@if ($term !== '')
    <div class="row">
        <div class="col-md-6">
            <div class="sa-card">
                <h2 class="h5 text-white mb-3">Companies</h2>
                @forelse ($companies as $company)
                    <div class="mb-3">
                        <a href="{{ route('superadmin.companies.show', $company) }}" class="text-white font-weight-bold">{{ $company->name }}</a>
                        <div class="sa-muted small">{{ $company->slug }} · {{ $company->email ?? 'no email' }}</div>
                    </div>
                @empty
                    <p class="sa-muted mb-0">No companies matched.</p>
                @endforelse
            </div>
        </div>
        <div class="col-md-6">
            <div class="sa-card">
                <h2 class="h5 text-white mb-3">Users</h2>
                @forelse ($users as $user)
                    <div class="mb-3">
                        <div class="text-white font-weight-bold">{{ $user->name }}</div>
                        <div class="sa-muted small">
                            {{ $user->email }}
                            · {{ $user->is_super_admin ? 'Super Admin' : ($user->company?->name ?? 'No company') }}
                        </div>
                        @if ($user->company_id)
                            <a href="{{ route('superadmin.companies.show', $user->company_id) }}" class="small">View company</a>
                        @endif
                    </div>
                @empty
                    <p class="sa-muted mb-0">No users matched.</p>
                @endforelse
            </div>
        </div>
    </div>
@endif
@endsection
