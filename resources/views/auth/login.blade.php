@extends('layouts.guest', ['title' => 'Masuk'])

@section('content')
    <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand">Panel Administrasi</p>
    <h1 class="mt-3 text-3xl font-black leading-[1.1] tracking-tight text-black-soft sm:text-4xl">Masuk</h1>
    <p class="mt-3 text-sm leading-relaxed text-muted">Gunakan akun pengelola untuk mengakses dashboard. Akses terbatas untuk pengelola resmi.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" novalidate>
        @csrf
        <x-ui.input name="email" type="email" label="Email" icon="mail" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
        <x-ui.input name="password" type="password" label="Kata Sandi" icon="lock" required autocomplete="current-password" placeholder="Masukkan kata sandi" />

        <x-ui.button type="submit" class="mt-2 w-full !py-3.5 uppercase tracking-wider shadow-[0_8px_20px_-8px_rgb(217_0_0/0.7)]">
            Masuk
            <x-ui.icon name="arrow-right" class="size-4" />
        </x-ui.button>
    </form>
@endsection
