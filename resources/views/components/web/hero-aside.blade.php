{{-- Props: title (baris putih), highlight (baris merah), eyebrow, scrollTo (tautan petunjuk gulir, opsional), scrollLabel (label aksesibilitas tombol gulir). Slot = deskripsi; slot bernama aside = isi sisi kanan (mis. <x-web.gallery-mosaic>). Hero halaman dalam bertema gelap: latar hitam dengan cahaya merah tipis dan kisi samar, teks di kiri, aside di kanan, satu layar penuh. Butuh navbar transparan (transparentNav). --}}
@props(['title', 'highlight' => null, 'eyebrow' => null, 'scrollTo' => null, 'scrollLabel' => 'Gulir ke bagian berikutnya'])
<section class="relative isolate overflow-hidden bg-black-deep text-white lg:flex lg:min-h-[max(100vh,100dvh)] lg:items-center">
    {{-- Cahaya merah tipis dari kanan bawah dan garis kisi samar, supaya latar hitam tidak terasa kosong. --}}
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(60rem_40rem_at_85%_100%,color-mix(in_srgb,var(--color-brand)_22%,transparent),transparent)]" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 opacity-[0.07] [background-image:linear-gradient(to_right,white_1px,transparent_1px)] [background-size:6rem_100%]" aria-hidden="true"></div>
    <div class="absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-black-deep to-transparent" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-brand" aria-hidden="true"></div>
    @if ($scrollTo)
        <x-web.scroll-hint :href="$scrollTo" :label="$scrollLabel" />
    @endif

    <div class="container-site grid items-center gap-12 py-28 sm:py-32 lg:grid-cols-[1fr_1.1fr] lg:gap-16 lg:py-24">
        <div data-hero class="border-l-4 border-brand-bright pl-5 sm:pl-8">
            @if ($eyebrow)
                <p class="text-sm font-bold uppercase tracking-widest text-brand-bright">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-4 text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl">
                <span class="block">{{ $title }}</span>
                @if ($highlight)
                    <span class="block text-brand-bright">{{ $highlight }}</span>
                @endif
            </h1>
            @if (trim($slot))
                <p class="mt-6 max-w-lg text-base leading-relaxed text-white/80 sm:text-lg">{{ $slot }}</p>
            @endif
        </div>

        <div class="hero-in" style="--hero-delay: 350ms">{{ $aside ?? '' }}</div>
    </div>
</section>
