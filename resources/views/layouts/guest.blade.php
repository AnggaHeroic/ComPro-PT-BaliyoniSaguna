{{-- Layout halaman tamu (login). Variabel: $title (opsional). Dua kolom: panel merek hitam-merah bertema keamanan (lg ke atas) dan area formulir terang berkisi. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | '.$company['name'] : $company['name'] }}</title>
    {{-- Halaman internal: jangan diindeks mesin pencari. --}}
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#000000">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-ink">
    <div class="grid min-h-dvh lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
        {{-- Panel merek (lg ke atas). Seluruh hiasan bersifat dekoratif. --}}
        <aside class="relative isolate hidden flex-col justify-between overflow-hidden bg-black-soft p-12 text-white lg:flex xl:p-16">
            <div class="absolute inset-0 -z-20 bg-linear-to-br from-black-soft via-black-soft to-[#4a0000]" aria-hidden="true"></div>
            <div class="absolute inset-0 -z-20 bg-[radial-gradient(ellipse_60%_50%_at_100%_100%,rgb(217_0_0/0.35),transparent_70%)]" aria-hidden="true"></div>
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgb(255_255_255/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.06)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:radial-gradient(ellipse_at_30%_30%,black_10%,transparent_75%)]" aria-hidden="true"></div>
            <span class="absolute inset-y-0 left-0 w-1 bg-brand" aria-hidden="true"></span>

            <a href="{{ route('home') }}" class="flex w-fit items-center gap-3" aria-label="{{ $company['name'] }}, ke beranda">
                <x-web.logo size="size-12" />
                <x-web.wordmark tone="light" class="!text-[1.3rem]" />
            </a>

            <div class="max-w-md">
                <h2 class="font-display text-3xl font-black leading-[1.1] tracking-tight xl:text-4xl">Kelola konten website dengan mudah.</h2>
                <p class="mt-4 text-sm leading-relaxed text-white/70">Perbarui layanan, galeri, dan informasi perusahaan dari satu tempat tanpa menyentuh kode.</p>

                {{-- Mockup abstrak panel admin dengan monitor (kiri bawah) dan kartu status (kanan bawah) yang menimpa tepinya. --}}
                <div class="relative mt-10 pb-10 pr-4" aria-hidden="true">
                    <div class="border border-white/15 bg-white/[0.05] backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 border-b border-white/10 px-3 py-2.5">
                            <span class="size-2 bg-brand-bright"></span>
                            <span class="size-2 bg-white/30"></span>
                            <span class="size-2 bg-white/15"></span>
                            <span class="ml-3 h-1.5 w-24 bg-white/15"></span>
                        </div>
                        <div class="flex">
                            <div class="w-14 shrink-0 space-y-2.5 border-r border-white/10 p-3">
                                <span class="block h-1.5 bg-brand-bright"></span>
                                <span class="block h-1.5 bg-white/20"></span>
                                <span class="block h-1.5 bg-white/20"></span>
                                <span class="block h-1.5 bg-white/20"></span>
                            </div>
                            <div class="flex-1 space-y-3 p-4">
                                <span class="block h-2 w-1/3 bg-white/50"></span>
                                <div class="grid grid-cols-3 gap-2.5">
                                    @foreach (['layers', 'image', 'briefcase'] as $icon)
                                        <div class="space-y-2 border border-white/10 bg-white/[0.04] p-2.5">
                                            <x-ui.icon :name="$icon" class="size-4 text-white/70" />
                                            <span class="block h-1.5 w-2/3 bg-white/25"></span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="space-y-2 border border-white/10 bg-white/[0.04] p-3">
                                    <span class="block h-1.5 w-full bg-white/20"></span>
                                    <span class="block h-1.5 w-5/6 bg-white/15"></span>
                                    <span class="block h-1.5 w-2/3 bg-white/10"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-3 -left-6 w-44 drop-shadow-[0_16px_30px_rgb(0_0_0/0.7)]">
                        <div class="border border-white/25 bg-black-deep p-1.5">
                            <div class="aspect-video space-y-2 bg-white/[0.07] p-2.5">
                                <span class="block h-1.5 w-1/3 bg-brand-bright"></span>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <span class="block h-6 bg-white/15"></span>
                                    <span class="block h-6 bg-white/10"></span>
                                </div>
                                <span class="block h-1 w-full bg-white/20"></span>
                                <span class="block h-1 w-2/3 bg-white/10"></span>
                            </div>
                        </div>
                        <span class="mx-auto block h-3 w-4 bg-white/20"></span>
                        <span class="mx-auto block h-1 w-20 bg-white/35"></span>
                    </div>
                    <div class="absolute bottom-0 right-0 flex w-44 items-center gap-3 bg-white p-3 text-black-soft shadow-[0_16px_40px_-8px_rgb(0_0_0/0.6)]">
                        <span class="flex size-8 shrink-0 items-center justify-center bg-brand text-white"><x-ui.icon name="check" class="size-4" /></span>
                        <span class="min-w-0 flex-1 space-y-1.5">
                            <span class="block h-1.5 w-full bg-black-soft/70"></span>
                            <span class="block h-1.5 w-2/3 bg-black-soft/20"></span>
                        </span>
                    </div>
                </div>
            </div>

            <p class="text-xs text-white/50">&copy; {{ date('Y') }} {{ $company['name'] }}</p>
        </aside>

        {{-- Area formulir: kisi tipis yang memudar di belakang form. --}}
        <main class="relative isolate flex flex-col justify-center bg-white px-6 py-10 sm:px-12">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgb(11_11_12/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(11_11_12/0.05)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:radial-gradient(ellipse_at_center,black_20%,transparent_80%)]" aria-hidden="true"></div>
            <div class="mx-auto w-full max-w-sm">
                <a href="{{ route('home') }}" class="mb-10 flex w-fit items-center gap-3 lg:hidden" aria-label="{{ $company['name'] }}, ke beranda">
                    <x-web.logo size="size-10" />
                    <x-web.wordmark />
                </a>

                @yield('content')

                <a href="{{ route('home') }}" class="group mt-8 inline-flex items-center gap-2 text-sm font-semibold text-muted transition-colors hover:text-brand">
                    <x-ui.icon name="arrow-left" class="size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali ke website
                </a>
            </div>
        </main>
    </div>
</body>
</html>
