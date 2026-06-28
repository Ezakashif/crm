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
    @stack('js')
@stop
