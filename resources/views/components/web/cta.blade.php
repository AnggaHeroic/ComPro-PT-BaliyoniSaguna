{{-- Props: company (profil; memakai name, phone, phone_href, mobile, mobile_url (wa.me), email, address, maps_url). Ajakan menghubungi perusahaan dan ringkasan kontak dalam satu kartu ringkas, tepat di atas footer. --}}
@props(['company'])
<section class="bg-gray-tint py-12 lg:py-16">
    <div class="container-site">
        {{-- Garis merah tebal di atas kartu, selaras dengan garis di atas footer. --}}
        <div class="mx-auto max-w-6xl border border-t-4 border-black-soft/10 border-t-brand bg-white">
            {{-- Ajakan --}}
            <div class="flex flex-col gap-6 p-8 lg:flex-row lg:items-center lg:justify-between lg:p-10">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand">Hubungi Kami</p>
                    <h2 class="mt-3 text-2xl font-black leading-tight tracking-tight text-black-soft sm:text-3xl">
                        Butuh solusi untuk <span class="text-brand">kebutuhan Anda?</span>
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-muted sm:text-base">Sampaikan kebutuhan Anda kepada kami, tim kami siap membantu.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row lg:shrink-0">
                    {{-- Menuju halaman kontak (belum tersedia). --}}
                    <x-ui.button href="/kontak">
                        Kontak Kami
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </x-ui.button>
                    @if ($company['phone'])
                        <x-ui.button :href="'tel:'.$company['phone_href']" variant="outline">Telepon Kami</x-ui.button>
                    @endif
                </div>
            </div>

            {{-- Ringkasan kontak: tiga kolom, isi rata tengah dengan ikon bulat di atas. Ukuran ikon dan teks sama di ketiganya. --}}
            <div class="grid divide-y divide-black-soft/10 border-t border-black-soft/10 lg:grid-cols-3 lg:divide-x lg:divide-y-0">
                {{-- Telepon dan WhatsApp satu kelompok (seperti footer); div karena tiap nomor punya tautan sendiri. --}}
                @if ($company['phone'] || $company['mobile'])
                    <div class="group flex flex-col items-center p-6 text-center lg:px-8">
                        {{-- Ikon ikut berubah merah bila salah satu nomor (telepon atau WA) disorot. --}}
                        <span class="flex size-11 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-has-[a:hover]:bg-brand">
                            <x-ui.icon name="phone" class="size-5" />
                        </span>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Telepon</p>
                        <div class="mt-1 space-y-0.5">
                            @if ($company['phone'])
                                <a href="tel:{{ $company['phone_href'] }}" class="block text-sm font-semibold leading-6 text-black-soft transition-colors hover:text-brand">Telp. {{ $company['phone'] }}</a>
                            @endif
                            @if ($company['mobile'])
                                <a href="{{ $company['mobile_url'] }}" target="_blank" rel="noopener noreferrer" class="block text-sm font-semibold leading-6 text-black-soft transition-colors hover:text-brand"
                                   aria-label="Hubungi lewat WhatsApp {{ $company['mobile'] }}">WA. {{ $company['mobile'] }}</a>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($company['email'])
                    <a href="mailto:{{ $company['email'] }}" class="group flex flex-col items-center p-6 text-center lg:px-8">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-hover:bg-brand">
                            <x-ui.icon name="mail" class="size-5" />
                        </span>
                        <span class="mt-3 block text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Email</span>
                        <span class="mt-1 block break-all text-sm font-semibold leading-6 text-black-soft transition-colors group-hover:text-brand">{{ $company['email'] }}</span>
                    </a>
                @endif
                @if ($company['address'])
                    <a href="{{ $company['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center p-6 text-center lg:px-8"
                       aria-label="Lihat alamat {{ $company['name'] }} di Google Maps">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black-soft text-white transition-colors duration-200 group-hover:bg-brand">
                            <x-ui.icon name="map-pin" class="size-5" />
                        </span>
                        <span class="mt-3 block text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Alamat</span>
                        <span class="mt-1 block text-sm font-semibold leading-6 text-black-soft transition-colors group-hover:text-brand">{{ $company['address'] }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
