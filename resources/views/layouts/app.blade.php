@extends('adminlte::page')

@section('title', config('app.name'))

@section('content_header')
    @isset($header)
        {!! $header !!}
    @endisset
@stop

@section('content')
    {{ $slot }}
@stop

@section('css')
    @stack('css')
@stop

@section('js')
    @auth
        @if(auth()->user()->hasAnyPermission(['view.leads', 'view.customers']))
            @include('partials.global-search-autocomplete')
        @endif
    @endauth
    @stack('js')
@stop
