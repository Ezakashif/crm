@extends('adminlte::master')

@section('classes_body', 'lockscreen')

@section('body')
    <div class="lockscreen-wrapper">
        <div class="lockscreen-logo">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" height="50" alt="Logo">
                {!! config('adminlte.logo', '<b>CRM</b> Panel') !!}
            </a>
        </div>

        <div class="lockscreen-name">{{ Auth::user()->name ?? Auth::user()->email }}</div>

        <div class="lockscreen-item">
            <form method="POST" action="{{ route('password.confirm') }}" class="lockscreen-credentials ml-0">
                @csrf
                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="{{ __('Password') }}" required autofocus>
                    <div class="input-group-append">
                        <button type="submit" class="btn"><i class="fas fa-arrow-right text-muted"></i></button>
                    </div>
                </div>
            </form>
        </div>

        @error('password')
            <div class="lockscreen-subitem text-center"><b class="text-danger">{{ $message }}</b></div>
        @enderror

        <div class="help-block text-center">
            {{ __('This is a secure area. Please confirm your password before continuing.') }}
        </div>
    </div>
@stop
