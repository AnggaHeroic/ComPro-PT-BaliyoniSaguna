{{-- Layout halaman publik. Variabel: $title (opsional), $description (opsional), $transparentNav (default false). Data company/navigation/services disuplai view composer di AppServiceProvider. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' — '.$company['name'] : $company['name'] }}</title>
    <meta name="description" content="{{ $description ?? $company['name'].', '.$company['city'].'. '.$company['tagline'] }}">
    <meta property="og:title" content="{{ isset($title) ? $title.' — '.$company['name'] : $company['name'] }}">
    <meta property="og:description" content="{{ $description ?? $company['name'].', '.$company['city'].'. '.$company['tagline'] }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#000000">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-sm focus:bg-black-soft focus:px-4 focus:py-2 focus:text-white">Lewati ke konten</a>
    <x-web.navbar :company="$company" :navigation="$navigation" :services="$services" :transparent="$transparentNav ?? false" />
    <main id="konten" class="flex-1">
        @yield('content')
    </main>
    <x-web.footer :company="$company" :navigation="$navigation" />
</body>
</html>
