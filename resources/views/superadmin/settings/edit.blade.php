@extends('superadmin.layout')

@section('title', 'System settings')
@section('heading', 'System settings')
@section('subheading', 'Platform-wide configuration stored in the database')

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="sa-card">
            <form method="POST" action="{{ route('superadmin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Platform name</label>
                    <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? config('app.name')) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Platform logo</label>
                    @if ($logoUrl)
                        <div class="mb-2"><img src="{{ $logoUrl }}" alt="" style="height:48px;"></div>
                        <label class="sa-muted small d-block mb-2">
                            <input type="checkbox" name="remove_logo" value="1"> Remove logo
                        </label>
                    @endif
                    <input type="file" name="platform_logo" class="form-control-file text-white">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Default timezone</label>
                        <input type="text" name="default_timezone" value="{{ old('default_timezone', $settings['default_timezone'] ?? config('app.timezone')) }}" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Default currency</label>
                        <input type="text" name="default_currency" value="{{ old('default_currency', $settings['default_currency'] ?? 'USD') }}" class="form-control" maxlength="3" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Mail from name</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Mail from address</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Trial duration (days)</label>
                        <input type="number" name="trial_duration_days" value="{{ old('trial_duration_days', $settings['trial_duration_days'] ?? 14) }}" class="form-control" min="1" max="365" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Default company status</label>
                        <select name="default_company_status" class="custom-select" required>
                            @foreach (\App\Models\Company::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected(old('default_company_status', $settings['default_company_status'] ?? 'active') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="registration_enabled" name="registration_enabled" value="1" @checked(old('registration_enabled', ($settings['registration_enabled'] ?? '0') === '1'))>
                        <label class="custom-control-label" for="registration_enabled">Registration enabled</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" value="1" @checked(old('maintenance_mode', ($settings['maintenance_mode'] ?? '0') === '1'))>
                        <label class="custom-control-label" for="maintenance_mode">Maintenance mode</label>
                    </div>
                </div>

                <button class="btn btn-info">Save settings</button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="sa-card" id="announcement">
            <h2 class="h5 text-white mb-3">Broadcast announcement</h2>
            <p class="sa-muted small">Shown to tenant users on the CRM dashboard.</p>
            <form method="POST" action="{{ route('superadmin.settings.announcement') }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <textarea name="broadcast_announcement" rows="5" class="form-control" placeholder="Optional platform-wide message">{{ old('broadcast_announcement', $settings['broadcast_announcement'] ?? '') }}</textarea>
                </div>
                <button class="btn btn-outline-light">Update announcement</button>
            </form>
        </div>
    </div>
</div>
@endsection
