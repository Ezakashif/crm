@php
    $home = config('marketing.home');
@endphp

<x-marketing-layout
    :title="null"
    :description="config('marketing.description')"
    body-class="index-page"
>
    @include('marketing.partials.home-sections')
</x-marketing-layout>
