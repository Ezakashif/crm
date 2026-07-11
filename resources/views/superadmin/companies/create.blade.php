@extends('superadmin.layout')

@section('title', 'New company')
@section('heading', 'Create company')
@section('subheading', 'Provision a tenant and optional first admin')

@section('content')
<div class="sa-card" style="max-width: 760px;">
    <form method="POST" action="{{ route('superadmin.companies.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Company name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Slug <span class="sa-muted">(optional)</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="acme-crm">
            </div>
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label>Logo</label>
                <input type="file" name="logo" class="form-control-file text-white">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Status</label>
                <select name="status" class="custom-select" required>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Subscription</label>
                <select name="subscription_status" class="custom-select" required>
                    @foreach ($subscriptionStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('subscription_status', 'trial') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Plan</label>
                <select name="plan_id" class="custom-select">
                    <option value="">Default</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((string) old('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Trial ends at <span class="sa-muted">(optional)</span></label>
            <input type="datetime-local" name="trial_ends_at" value="{{ old('trial_ends_at') }}" class="form-control">
        </div>

        <hr style="border-color: #1f2937;">
        <h2 class="h6 text-white">First company admin <span class="sa-muted">(optional)</span></h2>

        <div class="form-group">
            <label>Admin name</label>
            <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Admin email</label>
            <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Admin password</label>
            <input type="password" name="admin_password" class="form-control">
        </div>

        <div class="d-flex">
            <button class="btn btn-info mr-2">Create company</button>
            <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>
@endsection
