{{-- Props: title (baris putih), highlight (baris merah di bawah title), eyebrow, image (ID foto Picsum PLACEHOLDER). Slot = deskripsi. Hero halaman dalam, diadaptasi dari varian "split" hero Layanan & Jasa di project Bali Oase Semesta (navy diganti hitam): foto penuh, panel gelap semi-transparan bersisi miring, pita terang, dan dua pita merah memudar. Butuh navbar transparan (transparentNav). --}}
@props(['title', 'highlight' => null, 'eyebrow' => null, 'image' => 1048])
{{-- --hero-edge = posisi x tepi miring di bagian atas hero (62% lebar, dijaga agar tidak melewati tepi kanan). Semua pita dihitung dari nilai ini. --}}
<section class="relative isolate flex min-h-[max(100vh,100dvh)] items-center overflow-hidden bg-black-deep text-white [--hero-edge:min(62%,100%_-_2rem)]">
    {{-- Foto PLACEHOLDER sementara (Picsum). Ganti dengan foto resmi perusahaan bila tersedia. Foto memenuhi seluruh lebar hero; di desktop bagian kirinya tertutup panel gelap semi-transparan. --}}
    <img src="https://picsum.photos/id/{{ $image }}/1920/900" alt="" class="absolute inset-0 -z-30 size-full object-cover" fetchpriority="high" referrerpolicy="no-referrer">
    {{-- Lapisan hitam yang menipis ke kanan, desaturasi sisi kanan saja (abu-abu dengan blend saturation), dan gradasi gelap dari bawah. --}}
    <div class="absolute inset-0 -z-30 bg-linear-to-r from-black-deep/55 from-50% to-black-deep/20" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-30 bg-linear-to-r from-transparent from-50% to-neutral-500/70 mix-blend-saturation" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-30 bg-linear-to-t from-black-deep/60 to-transparent" aria-hidden="true"></div>
    {{-- Mobile: foto ditutup gradasi gelap.
         Desktop: panel gelap (opasitas 70%) selebar --hero-edge dengan sisi kanan miring, ditambah tiga pita sejajar di tepinya:
         pita terang, pita merah lebar yang memudar (mulai 28% tinggi), dan garis merah tipis (mulai 42% tinggi), keduanya digeser ke sisi foto. --}}
    <div class="absolute inset-0 -z-10 bg-linear-to-r from-black-deep from-15% via-black-deep/90 via-50% to-black-deep/60 lg:hidden" aria-hidden="true"></div>
    <div class="absolute inset-y-0 left-0 -z-10 hidden w-(--hero-edge) bg-black-deep/70 [clip-path:polygon(0_0,100%_0,calc(100%-8rem)_100%,0_100%)] lg:block" aria-hidden="true"></div>
    <div class="absolute inset-y-0 left-0 -z-10 hidden w-[calc(var(--hero-edge)+6rem)] bg-white/10 [clip-path:polygon(calc(100%-6rem)_0,100%_0,calc(100%-8rem)_100%,calc(100%-14rem)_100%)] lg:block" aria-hidden="true"></div>
    <div class="absolute inset-y-0 left-0 -z-10 hidden w-[calc(var(--hero-edge)+8rem)] bg-linear-to-b from-brand/90 from-28% via-brand/50 to-brand/10 [clip-path:polygon(calc(100%-7.24rem)_28%,calc(100%-2.24rem)_28%,calc(100%-8rem)_100%,calc(100%-13rem)_100%)] lg:block" aria-hidden="true"></div>
    <div class="absolute inset-y-0 left-0 -z-10 hidden w-[calc(var(--hero-edge)+10rem)] bg-linear-to-b from-brand/80 from-42% to-brand/10 [clip-path:polygon(calc(100%-4.36rem)_42%,calc(100%-3.36rem)_42%,calc(100%-8rem)_100%,calc(100%-9rem)_100%)] lg:block" aria-hidden="true"></div>
    {{-- Gradient atas agar navbar transparan tetap terbaca di atas foto. --}}
    <div class="absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-black-deep/70 to-transparent" aria-hidden="true"></div>
    {{-- Strip aksen di tepi bawah, seperti hero Beranda. --}}
    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-brand" aria-hidden="true"></div>

    <div class="container-site py-28 sm:py-32">
        <div class="border-l-4 border-brand-bright pl-5 sm:pl-8">
            @if ($eyebrow)
                <p class="text-sm font-bold uppercase tracking-widest text-brand-bright">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-4 text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl lg:text-4xl xl:text-5xl">
                <span class="block">{{ $title }}</span>
                @if ($highlight)
                    <span class="block text-brand-bright">{{ $highlight }}</span>
                @endif
            </h1>
            @if (trim($slot))
                <p class="mt-6 max-w-lg text-base leading-relaxed text-white/80 sm:text-lg">{{ $slot }}</p>
            @endif
        </div>
    </div>
</section>
