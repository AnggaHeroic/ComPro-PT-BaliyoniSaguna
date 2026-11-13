{{-- Props: image (ID foto Unsplash PLACEHOLDER), formTo (tautan tombol utama, ke formulir), mapTo (tautan tombol kedua, ke peta; kosongkan bila tidak ada), scrollTo (tautan petunjuk gulir). Hero halaman Kontak: teks rata tengah di atas foto gelap, dengan garis vertikal tipis yang turun ke persimpangan garis horizontal selebar layar (berlian merah) di atas label, dan penutup garis serupa di bawah tombol. Sengaja tanpa data kontak karena sudah ada di bagian bawah halaman. Butuh navbar transparan (transparentNav). --}}
@props(['image' => '1699884745550-07d309f2f61d', 'formTo' => null, 'mapTo' => null, 'scrollTo' => null])
<section class="relative isolate flex min-h-[max(100vh,100dvh)] items-center overflow-hidden bg-black-deep text-white">
    <img src="https://images.unsplash.com/photo-{{ $image }}?auto=format&fit=crop&w=1920&h=1080&q=75" alt="" class="absolute inset-0 -z-30 size-full object-cover" fetchpriority="high" referrerpolicy="no-referrer">
    {{-- Foto didesaturasi dan digelapkan merata, vignette menggelapkan tepi, rona merah tipis naik dari bawah. --}}
    <div class="absolute inset-0 -z-30 bg-black-deep/75" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-30 bg-neutral-500/60 mix-blend-saturation" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-20 bg-[radial-gradient(ellipse_at_center,transparent_30%,rgb(0_0_0/0.65)_100%)]" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-20 bg-linear-to-t from-brand/25 via-transparent to-transparent" aria-hidden="true"></div>
    <div class="absolute inset-x-0 top-0 -z-20 h-44 bg-linear-to-b from-black-deep/80 to-transparent" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-brand" aria-hidden="true"></div>

    <div class="container-site py-28 text-center sm:py-32">
        <div data-hero class="mx-auto flex max-w-3xl flex-col items-center">
            {{-- Garis vertikal turun ke persimpangan dengan garis horizontal selebar layar yang memudar di kedua ujung; berlian merah menandai titik temunya. --}}
            <span class="flex flex-col items-center" aria-hidden="true">
                <span class="grow-y-in h-14 w-px bg-linear-to-b from-transparent to-brand-bright"></span>
                <span class="flex w-screen items-center">
                    <span class="grow-x-in origin-right h-px flex-1 bg-linear-to-r from-transparent to-white/25"></span>
                    <span class="mx-4 size-2 rotate-45 bg-brand-bright"></span>
                    <span class="grow-x-in origin-left h-px flex-1 bg-linear-to-l from-transparent to-white/25"></span>
                </span>
            </span>
            <p class="mt-5 text-sm font-bold uppercase tracking-[0.3em] text-brand-bright">Kontak Kami</p>
            <h1 class="mt-5 text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                <span class="block">Wujudkan Kebutuhan</span>
                <span class="block text-brand-bright">Bersama Kami</span>
            </h1>
            <p class="mt-6 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg">
                Sampaikan kebutuhan atau pertanyaan Anda. Tim kami siap membantu dan akan menjawab secepatnya.
            </p>
            @if ($formTo || $mapTo)
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    @if ($formTo)
                        <x-ui.button :href="$formTo">
                            Kirim Pesan
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </x-ui.button>
                    @endif
                    @if ($mapTo)
                        <x-ui.button :href="$mapTo" variant="outline-light">Lihat Lokasi</x-ui.button>
                    @endif
                </div>
            @endif
            {{-- Penutup bergaya sama: garis horizontal memudar dengan berlian kosong di tengah. --}}
            <span class="mt-10 flex w-screen items-center" aria-hidden="true">
                <span class="h-px flex-1 bg-linear-to-r from-transparent to-white/25"></span>
                <span class="mx-4 size-2 rotate-45 border border-brand-bright"></span>
                <span class="h-px flex-1 bg-linear-to-l from-transparent to-white/25"></span>
            </span>
        </div>
    </div>

    @if ($scrollTo)
        <x-web.scroll-hint :href="$scrollTo" label="Gulir ke informasi kontak" />
    @endif
</section>
