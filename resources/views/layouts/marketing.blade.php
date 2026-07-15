<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-marketing.seo
        :title="$title ?? null"
        :description="$description ?? null"
        :image="$ogImage ?? null"
    />

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/marketing.css', 'resources/js/marketing.js'])

    @stack('head')
</head>
<body class="marketing-body antialiased">
    <a class="mk-skip-link" href="#main-content">Skip to content</a>

    <x-marketing.navbar />

    <main id="main-content">
        {{ $slot }}
    </main>

    <x-marketing.footer />

    @stack('scripts')
</body>
</html>
