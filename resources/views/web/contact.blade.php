@extends('layouts.public', ['transparentNav' => true, 'title' => 'Kontak Kami', 'description' => 'Hubungi PT Baliyoni Saguna: telepon, WhatsApp, email, alamat kantor, dan formulir pesan.'])

@section('content')
    <x-web.contact-hero formTo="#informasi-kontak" :mapTo="$company['address'] ? '#lokasi' : null" scrollTo="#informasi-kontak" />

    <section id="informasi-kontak" class="scroll-mt-20 bg-white py-20 lg:py-28">
        <div class="container-site grid gap-14 lg:grid-cols-12 lg:gap-16">
            {{-- Informasi kontak tanpa kartu; di layar lebar diberi jarak atas (padding + tepi formulir) agar judulnya sejajar dengan kolom pertama formulir. Baris bergaris tipis, ikon hitam berubah merah saat disorot. --}}
            <div data-reveal class="lg:col-span-5 lg:pt-[calc(2rem+4px)]">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand">Informasi Kontak</p>
                <h2 class="mt-3 text-balance text-2xl font-black leading-tight tracking-tight text-black-soft sm:text-3xl">Jangkau Kami dengan Mudah</h2>

                <ul class="mt-6 divide-y divide-black-soft/10 border-y border-black-soft/10">
                    @if ($company['phone'] || $company['mobile'])
                        <li class="group flex gap-4 py-5">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-has-[a:hover]:bg-brand">
                                <x-ui.icon name="phone" class="size-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Telepon dan WhatsApp</p>
                                @if ($company['phone'])
                                    <a href="tel:{{ $company['phone_href'] }}" class="mt-1 block text-sm font-semibold leading-6 text-black-soft transition-colors hover:text-brand">Telp. {{ $company['phone'] }}</a>
                                @endif
                                @if ($company['mobile'])
                                    <a href="{{ $company['mobile_url'] }}" target="_blank" rel="noopener noreferrer" class="block text-sm font-semibold leading-6 text-black-soft transition-colors hover:text-brand"
                                       aria-label="Hubungi lewat WhatsApp {{ $company['mobile'] }}">WA. {{ $company['mobile'] }}</a>
                                @endif
                            </div>
                        </li>
                    @endif
                    @if ($company['email'])
                        <li>
                            <a href="mailto:{{ $company['email'] }}" class="group flex gap-4 py-5">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-hover:bg-brand">
                                    <x-ui.icon name="mail" class="size-5" />
                                </span>
                                <span>
                                    <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Email</span>
                                    <span class="mt-1 block break-all text-sm font-semibold leading-6 text-black-soft transition-colors group-hover:text-brand">{{ $company['email'] }}</span>
                                </span>
                            </a>
                        </li>
                    @endif
                    @if ($company['address'])
                        <li>
                            <a href="{{ $company['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="group flex gap-4 py-5"
                               aria-label="Lihat alamat {{ $company['name'] }} di Google Maps">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-hover:bg-brand">
                                    <x-ui.icon name="map-pin" class="size-5" />
                                </span>
                                <span>
                                    <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Alamat</span>
                                    <span class="mt-1 block text-sm font-semibold leading-6 text-black-soft transition-colors group-hover:text-brand">{{ $company['address'] }}</span>
                                </span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            @if ($company['mobile_url'])
                <div data-reveal class="lg:col-span-7">
                    <x-web.contact-form :company="$company" :subject="$subject" />
                </div>
            @endif
        </div>
    </section>

    @if ($company['address'])
        {{-- Peta lokasi (embed Google Maps dari alamat). --}}
        <section id="lokasi" class="scroll-mt-20 bg-gray-tint py-20 lg:py-24">
            <div class="container-site">
                <x-web.section-title eyebrow="Lokasi" title="Kantor Kami" center />
                <div data-reveal class="mt-12 overflow-hidden border border-black-soft/10 bg-white">
                    <iframe src="https://www.google.com/maps?q={{ urlencode($company['address']) }}&output=embed"
                            title="Lokasi {{ $company['name'] }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            class="block aspect-[4/3] w-full border-0 grayscale-[20%] lg:aspect-[16/7]"></iframe>
                </div>
                <p class="mt-4 text-center">
                    <a href="{{ $company['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
                        Buka di Google Maps
                        <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </p>
            </div>
        </section>
    @endif
@endsection
