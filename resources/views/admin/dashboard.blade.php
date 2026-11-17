@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <x-admin.page-header eyebrow="{{ $summary['today'] }}" title="Selamat datang, {{ auth()->user()->name }}" description="Ringkasan konten website {{ $company['short_name'] }}." />

        <section aria-label="Ringkasan" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($summary['stats'] as $stat)
                <x-admin.stat-card :label="$stat['label']" :value="$stat['value']" :hint="$stat['hint']" :icon="$stat['icon']" />
            @endforeach
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="flex flex-col border border-black-soft/10 bg-white lg:col-span-2" aria-labelledby="profil-perusahaan">
                <div class="flex h-14 items-center justify-between border-b border-black-soft/10 px-5 sm:px-6">
                    <h2 id="profil-perusahaan" class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Profil Perusahaan</h2>
                    <span class="border border-black-soft/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted">Hanya baca</span>
                </div>
                <dl class="flex-1 divide-y divide-black-soft/10 text-sm">
                    @foreach ([
                        'Nama' => $company['name'],
                        'Lokasi' => $company['city'],
                        'Email' => $company['email'],
                        'Telepon' => $company['phone'],
                        'WhatsApp' => $company['mobile'],
                        'Alamat' => $company['address'],
                    ] as $label => $value)
                        @if (filled($value))
                            <div class="grid gap-1 px-5 py-3.5 sm:grid-cols-[8rem_1fr] sm:gap-4 sm:px-6">
                                <dt class="font-semibold text-muted">{{ $label }}</dt>
                                <dd class="text-black-soft">{{ $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
                <p class="border-t border-black-soft/10 bg-gray-tint px-5 py-3 text-xs text-muted sm:px-6">Data ini masih dibaca dari konfigurasi. Pengelolaan lewat CMS akan tersedia pada tahap berikutnya.</p>
            </section>

            <section class="flex flex-col border border-black-soft/10 bg-white" aria-labelledby="pintasan">
                <div class="flex h-14 items-center border-b border-black-soft/10 px-5 sm:px-6">
                    <h2 id="pintasan" class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Pintasan Website</h2>
                </div>
                <ul class="flex-1 divide-y divide-black-soft/10">
                    @foreach ([
                        'Beranda' => route('home'),
                        'Layanan' => route('services'),
                        'Tentang Kami' => route('about'),
                        'Galeri' => route('gallery'),
                        'Kontak' => route('contact'),
                    ] as $label => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="group flex items-center justify-between px-5 py-3.5 text-sm font-semibold text-black-soft transition-colors hover:bg-gray-tint hover:text-brand sm:px-6">
                                {{ $label }}
                                <x-ui.icon name="arrow-right" class="size-4 text-black-soft/30 transition-all group-hover:translate-x-1 group-hover:text-brand" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <p class="border-t border-black-soft/10 bg-gray-tint px-5 py-3 text-xs text-muted sm:px-6">Setiap tautan terbuka di tab baru.</p>
            </section>
        </div>
    </div>
@endsection
