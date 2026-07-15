@extends('adminlte::master')

@section('classes_body', 'lockscreen')

@section('body')
    <div class="lockscreen-wrapper crm-auth-lockscreen">
        <div class="lockscreen-logo">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" height="50" alt="{{ config('adminlte.logo_img_alt', 'Logo') }}">
                {!! config('adminlte.logo', '<b>CRM</b> Panel') !!}
            </a>
        </div>

        <div class="lockscreen-name">{{ Auth::user()->name ?? Auth::user()->email }}</div>

        <p class="crm-auth-lead text-center">{{ __('Confirm your password to continue.') }}</p>

        <div class="lockscreen-item">
            <form method="POST" action="{{ route('password.confirm') }}" class="lockscreen-credentials ml-0 w-100">
                @csrf
                <label class="crm-auth-label" for="password">{{ __('Password') }}</label>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="{{ __('Password') }}" required autofocus autocomplete="current-password">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" aria-label="{{ __('Confirm') }}">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @error('password')
            <div class="lockscreen-subitem text-center"><b class="text-danger">{{ $message }}</b></div>
        @enderror

        <div class="help-block text-center text-muted">
            {{ __('This is a secure area. Please confirm your password before continuing.') }}
        </div>
    </div>
@stop
