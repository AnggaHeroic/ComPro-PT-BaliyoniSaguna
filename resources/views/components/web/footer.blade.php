{{-- Props: company (profil; phone, mobile, email, address; has_contact menentukan kolom kontak; description = deskripsi singkat di bawah logo), navigation (daftar menu) --}}
@props(['company', 'navigation'])
{{-- Footer hitam pekat dengan garis merah di atas, selaras dengan blok hitam pada logo. --}}
<footer id="kontak" class="border-t-4 border-brand bg-black-deep text-white/70">
    <div @class(['container-site grid gap-12 py-16', 'lg:grid-cols-12 lg:gap-16' => $company['has_contact'], 'md:grid-cols-2' => ! $company['has_contact']])>
        <div @class(['lg:col-span-5' => $company['has_contact']])>
            <div class="flex items-center gap-3">
                <x-web.logo size="size-14" />
                <x-web.wordmark tone="light" size="lg" />
            </div>
            <p class="mt-6 max-w-md text-sm leading-7">{{ $company['description'] }}</p>
        </div>

        <nav aria-label="Navigasi footer" @class(['lg:col-span-3' => $company['has_contact']])>
            <h2 class="font-sans text-xs font-bold uppercase tracking-[0.2em] text-white">Navigasi</h2>
            <span class="mt-3 block h-0.5 w-8 bg-brand-bright" aria-hidden="true"></span>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ($navigation as $item)
                    <li><a href="{{ $item['href'] }}" class="transition-colors hover:text-white hover:underline hover:decoration-brand-bright hover:underline-offset-4">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
        </nav>

        @if ($company['has_contact'])
            <div class="lg:col-span-4">
                <h2 class="font-sans text-xs font-bold uppercase tracking-[0.2em] text-white">Kontak</h2>
                <span class="mt-3 block h-0.5 w-8 bg-brand-bright" aria-hidden="true"></span>
                <address class="mt-5 space-y-4 text-sm not-italic">
                    {{-- Telepon dan HP dijadikan satu baris dengan jarak rapat. --}}
                    @if ($company['phone'] || $company['mobile'])
                        <div class="flex items-center gap-3">
                            <x-ui.icon name="phone" class="size-4 shrink-0 text-brand-bright" />
                            <div class="space-y-0.5">
                                @if ($company['phone'])
                                    <a href="tel:{{ $company['phone_href'] }}" class="block transition-colors hover:text-white">Telp. {{ $company['phone'] }}</a>
                                @endif
                                @if ($company['mobile'])
                                    <a href="{{ $company['mobile_url'] }}" target="_blank" rel="noopener noreferrer" class="block transition-colors hover:text-white"
                                       aria-label="Hubungi lewat WhatsApp {{ $company['mobile'] }}">WA. {{ $company['mobile'] }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($company['email'])
                        <a href="mailto:{{ $company['email'] }}" class="group flex gap-3 transition-colors hover:text-white">
                            <x-ui.icon name="mail" class="mt-0.5 size-4 text-brand-bright" />
                            <span class="break-all">{{ $company['email'] }}</span>
                        </a>
                    @endif
                    @if ($company['address'])
                        <a href="{{ $company['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="group flex gap-3 transition-colors hover:text-white"
                           aria-label="Lihat alamat {{ $company['name'] }} di Google Maps">
                            <x-ui.icon name="map-pin" class="mt-0.5 size-4 text-brand-bright" />
                            <span class="leading-6">{{ $company['address'] }}</span>
                        </a>
                    @endif
                </address>
            </div>
        @endif
    </div>
    <div class="border-t border-white/10">
        <div class="container-site flex flex-col gap-1 py-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company['name'] }}. Hak cipta dilindungi.</p>
            <p>{{ $company['city'] }}</p>
        </div>
    </div>
</footer>
