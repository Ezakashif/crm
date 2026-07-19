<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d6efd">

    <x-marketing.seo
        :title="$title ?? null"
        :description="$description ?? null"
        :image="$ogImage ?? null"
    />
    <x-marketing.json-ld type="organization" />
    <x-marketing.json-ld type="website" />

    <link href="{{ asset('marketing/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('marketing/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link href="{{ asset('marketing/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('marketing/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('marketing/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('marketing/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('marketing/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('marketing/assets/css/main.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body class="{{ $bodyClass ?? 'index-page' }}">
    @include('marketing.partials.header')

    <main class="main">
        {{ $slot }}
    </main>

    @include('marketing.partials.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Back to top">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script src="{{ asset('marketing/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('marketing/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('marketing/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('marketing/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('marketing/assets/vendor/typed.js/typed.umd.js') }}"></script>
    <script src="{{ asset('marketing/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('marketing/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
