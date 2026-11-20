{{-- Layout panel admin (CMS). Variabel: $title (opsional). Konten lewat @yield('content'). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | Admin' : 'Admin' }} | {{ $company['name'] }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Ruang scrollbar selalu disediakan agar isi tidak bergeser saat halaman memendek (mis. hasil pencarian sedikit). --}}
    <style>html { scrollbar-gutter: stable; }</style>
</head>
<body class="min-h-screen bg-gray-tint">
    <x-admin.sidebar />

    <div class="flex min-h-screen flex-col lg:pl-64">
        <header x-data class="sticky top-0 z-30 border-b border-black-soft/10 bg-white px-5 sm:px-8">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button type="button" @click="$dispatch('toggle-sidebar')" aria-controls="sidebar-admin" class="-ml-2.5 inline-flex size-11 items-center justify-center text-black-soft transition-colors hover:bg-gray-tint lg:hidden">
                        <span class="sr-only">Buka menu admin</span>
                        <x-ui.icon name="menu" class="size-6" />
                    </button>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-muted">Panel Admin <span class="mx-2 text-black-soft/25">/</span> <span class="text-black-soft">{{ $title ?? 'Dashboard' }}</span></p>
                </div>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="group inline-flex items-center gap-2 border border-black-soft/15 px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-black-soft transition-colors hover:border-black-soft hover:bg-black-soft hover:text-white">
                    <span class="max-sm:sr-only">Lihat Website</span>
                    <x-ui.icon name="external" class="size-4" />
                </a>
            </div>
        </header>

        <main class="flex-1 px-5 py-8 sm:px-8 lg:py-10">
            <div class="mx-auto max-w-6xl">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-black-soft/10 px-5 py-5 text-xs text-muted sm:px-8">&copy; {{ date('Y') }} {{ $company['name'] }}. Panel administrasi.</footer>
    </div>

    {{-- Popup berada langsung di bawah <body>, di luar wadah konten, agar selalu tepat di tengah layar. --}}
    <x-ui.flash-message />
    <x-ui.confirm-dialog />
    <x-admin.idle-guard />
</body>
</html>
