@extends('adminlte::auth.register')

@section('auth_header', __('Create your workspace'))

@section('auth_body')
    <p class="crm-auth-lead">{{ __('Set up your company account and first admin user.') }}</p>

    <form action="{{ route('register') }}" method="post" class="crm-auth-form">
        @csrf

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="company_name">{{ __('Company name') }}</label>
            <div class="input-group">
                <input id="company_name" type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                       value="{{ old('company_name') }}" placeholder="{{ __('Company name') }}" required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-building" aria-hidden="true"></span></div>
                </div>
                @error('company_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="name">{{ __('Your name') }}</label>
            <div class="input-group">
                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="{{ __('Your name') }}" required autocomplete="name">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-user" aria-hidden="true"></span></div>
                </div>
                @error('name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="email">{{ __('Email') }}</label>
            <div class="input-group">
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autocomplete="username">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-envelope" aria-hidden="true"></span></div>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('Password') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock" aria-hidden="true"></span></div>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="input-group">
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                       placeholder="{{ __('Confirm password') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock" aria-hidden="true"></span></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
            {{ __('Create account') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <p class="crm-auth-footer-link mb-0">
        <a href="{{ route('login') }}">{{ __('I already have an account') }}</a>
    </p>
@endsection
