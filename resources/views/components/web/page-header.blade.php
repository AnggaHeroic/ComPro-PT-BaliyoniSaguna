{{-- Props: title (baris putih), highlight (baris merah di bawah title), eyebrow, image (ID foto Picsum PLACEHOLDER), variant (split = teks di kiri dengan panel miring, default; center = teks rata tengah di atas foto, dengan slot opsional label (pengganti eyebrow bila ada kata berwarna) dan scrollTo (tautan petunjuk gulir); keduanya layar penuh). Slot = deskripsi. Hero halaman dalam, diadaptasi dari varian "split" hero Layanan & Jasa di project Bali Oase Semesta (navy diganti hitam): foto penuh, panel gelap semi-transparan bersisi miring, pita terang, dan dua pita merah memudar. Butuh navbar transparan (transparentNav). --}}
@props(['title', 'highlight' => null, 'eyebrow' => null, 'image' => 1048, 'variant' => 'split'])
@if ($variant === 'center')
{{-- Varian center: teks rata tengah di atas foto yang digelapkan merata, tanpa panel miring, agar berbeda dari varian split tetapi tetap hitam, merah, dan tegas. --}}
<section class="relative isolate flex min-h-[max(100vh,100dvh)] items-center overflow-hidden bg-black-deep text-white">
    <img src="https://picsum.photos/id/{{ $image }}/1920/900" alt="" class="absolute inset-0 -z-30 size-full object-cover" fetchpriority="high" referrerpolicy="no-referrer">
    {{-- Foto digelapkan merata dan didesaturasi; rona merah tipis naik dari bawah agar senada dengan aksen merek. --}}
    <div class="absolute inset-0 -z-30 bg-black-deep/75" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-30 bg-neutral-500/60 mix-blend-saturation" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-20 bg-linear-to-t from-brand/25 via-transparent to-transparent" aria-hidden="true"></div>
    {{-- Gradient atas agar navbar transparan tetap terbaca di atas foto. --}}
    <div class="absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-black-deep/80 to-transparent" aria-hidden="true"></div>

    <div class="container-site py-28 text-center sm:py-32">
      {{-- Siku sudut merah membingkai blok teks (corner bracket). --}}
      <div class="relative mx-auto max-w-4xl px-6 py-10 sm:px-12 sm:py-14">
        <span class="absolute left-0 top-0 size-6 border-l-2 border-t-2 border-brand-bright" aria-hidden="true"></span>
        <span class="absolute right-0 top-0 size-6 border-r-2 border-t-2 border-brand-bright" aria-hidden="true"></span>
        <span class="absolute bottom-0 left-0 size-6 border-b-2 border-l-2 border-brand-bright" aria-hidden="true"></span>
        <span class="absolute bottom-0 right-0 size-6 border-b-2 border-r-2 border-brand-bright" aria-hidden="true"></span>
        {{-- Label: teks putih diapit garis merah. Slot label dipakai bila ada kata yang perlu diberi warna (mis. tanda "&" merah); jika tidak, prop eyebrow berwarna merah. --}}
        @if ($eyebrow || isset($label))
            <div class="flex items-center justify-center gap-4">
                <span class="h-px w-10 bg-brand-bright sm:w-16" aria-hidden="true"></span>
                <p @class(['text-sm font-bold uppercase tracking-[0.25em]', 'text-white' => isset($label), 'text-brand-bright' => ! isset($label)])>{{ $label ?? $eyebrow }}</p>
                <span class="h-px w-10 bg-brand-bright sm:w-16" aria-hidden="true"></span>
            </div>
        @endif
        <h1 @class(['mt-6' => $eyebrow || isset($label), 'text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl'])>
            <span class="block">{{ $title }}</span>
            @if ($highlight)
                <span class="block text-brand-bright">{{ $highlight }}</span>
            @endif
        </h1>
        {{-- Tanpa label, garis pendek merah menjadi penanda di bawah judul. --}}
        @unless ($eyebrow || isset($label))
            <span class="mx-auto mt-7 block h-0.5 w-14 bg-brand-bright" aria-hidden="true"></span>
        @endunless
        @if (trim($slot))
            <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg">{{ $slot }}</p>
        @endif
      </div>
    </div>
    {{-- Pita putih samar miring di kiri dan kanan, melengkapi siku sudut; hanya di layar lebar. --}}
    <span class="absolute inset-y-0 left-[7%] -z-10 hidden w-14 skew-x-[20deg] bg-white/[0.04] lg:block" aria-hidden="true"></span>
    <span class="absolute inset-y-0 right-[7%] -z-10 hidden w-14 -skew-x-[20deg] bg-white/[0.04] lg:block" aria-hidden="true"></span>
    {{-- Petunjuk gulir: lingkaran kecil berpanah yang melompat pelan di tengah bawah, menuju bagian berikutnya. animate-bounce dimatikan bila pengguna memilih mengurangi gerakan. --}}
    @isset($scrollTo)
        <a href="{{ $scrollTo }}" aria-label="Gulir ke bagian berikutnya"
           class="absolute bottom-8 left-1/2 hidden size-9 -translate-x-1/2 items-center justify-center rounded-full border border-white/40 text-white transition-colors hover:border-brand-bright hover:bg-brand-bright motion-safe:animate-bounce sm:flex">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </a>
    @endisset
    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-brand" aria-hidden="true"></div>
</section>
@else
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
@endif
