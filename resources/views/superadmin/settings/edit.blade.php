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

                <div class="sa-form-section">
                    <h2 class="sa-form-section__title">Platform identity</h2>
                    <p class="sa-form-section__hint">Name and logo shown across the Super Admin console.</p>

                    <div class="form-group">
                        <label class="sa-required">Platform name</label>
                        <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? config('app.name')) }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-0">
                        <label>Platform logo</label>
                        @if ($logoUrl)
                            <div class="sa-logo-preview">
                                <img src="{{ $logoUrl }}" alt="Platform logo">
                            </div>
                            <label class="sa-muted small d-block mb-2">
                                <input type="checkbox" name="remove_logo" value="1"> Remove logo
                            </label>
                        @endif
                        <input type="file" name="platform_logo" class="form-control-file text-white">
                        <small class="sa-muted d-block mt-1">PNG/JPG/SVG accepted. Backgrounds are removed automatically. Tip: run <code>php artisan platform:optimize-logo --force-packaged</code> to install the official Algos logo.</small>
                    </div>
                </div>

                <div class="sa-form-section">
                    <h2 class="sa-form-section__title">Defaults</h2>
                    <p class="sa-form-section__hint">Timezone, currency, mail, and trial defaults for new tenants.</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="sa-required">Default timezone</label>
                            <input type="text" name="default_timezone" value="{{ old('default_timezone', $settings['default_timezone'] ?? config('app.timezone')) }}" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="sa-required">Default currency</label>
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

                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label class="sa-required">Trial duration (days)</label>
                            <input type="number" name="trial_duration_days" value="{{ old('trial_duration_days', $settings['trial_duration_days'] ?? 14) }}" class="form-control" min="1" max="365" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="sa-required">Default company status</label>
                            <select name="default_company_status" class="custom-select" required>
                                @foreach (\App\Models\Company::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('default_company_status', $settings['default_company_status'] ?? 'active') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="sa-form-section">
                    <h2 class="sa-form-section__title">Access controls</h2>
                    <p class="sa-form-section__hint">Registration and maintenance switches for the platform.</p>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="hidden" name="registration_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="registration_enabled" name="registration_enabled" value="1" @checked(old('registration_enabled', ($settings['registration_enabled'] ?? '0') === '1'))>
                            <label class="custom-control-label" for="registration_enabled">Registration enabled</label>
                            <div class="sa-muted small mt-1">When on, visitors can open <code>/register</code> and create a new company workspace.</div>
                        </div>
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="hidden" name="email_verification_required" value="0">
                            <input type="checkbox" class="custom-control-input" id="email_verification_required" name="email_verification_required" value="1" @checked(old('email_verification_required', ($settings['email_verification_required'] ?? '1') === '1'))>
                            <label class="custom-control-label" for="email_verification_required">Require email verification</label>
                            <div class="sa-muted small mt-1">When on, public self-registration and unverified tenant users must confirm email before using the CRM. Super Admins and admin-provisioned accounts stay verified.</div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" value="1" @checked(old('maintenance_mode', ($settings['maintenance_mode'] ?? '0') === '1'))>
                            <label class="custom-control-label" for="maintenance_mode">Maintenance mode</label>
                            <div class="sa-muted small mt-1">When on, tenant users are blocked from the CRM. Super Admins can still use this panel.</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info">Save settings</button>
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
                <button type="submit" class="btn btn-outline-light">Update announcement</button>
            </form>
        </div>
    </div>
</div>
@endsection
