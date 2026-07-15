@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('Forgot your password?'))

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-info crm-keep-alert">{{ session('status') }}</div>
    @endif

    <p class="crm-auth-lead">{{ __('Enter your workspace slug and email to receive a reset link.') }}</p>

    <form action="{{ route('password.email') }}" method="post" class="crm-auth-form">
        @csrf

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="company">{{ __('Workspace slug') }}</label>
            <div class="input-group">
                <input id="company" type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                       value="{{ old('company') }}" placeholder="{{ __('Workspace slug') }}" required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-building" aria-hidden="true"></span></div>
                </div>
                @error('company')
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

        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
            {{ __('Email Password Reset Link') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="crm-auth-footer-link mb-0">
        <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
    </p>
@stop
