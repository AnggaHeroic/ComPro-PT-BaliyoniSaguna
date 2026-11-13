{{-- Props: title (wajib), eyebrow (label merah kecil), image (ID foto Unsplash, bagian setelah 'photo-'), crumbs (jejak halaman: daftar ['label', 'href'] dengan href null untuk halaman aktif). Slot = deskripsi singkat; slot bernama icon (opsional) = ikon: di desktop berada di tengah lingkaran besar yang membuka foto di kanan (sisi kiri gelap, batasnya cekungan berupa busur putih), di ponsel berupa lingkaran kecil di atas label; beri class size-full pada ikonnya. Header halaman detail: tidak sepenuh layar. --}}
@props(['title', 'eyebrow' => null, 'image' => null, 'crumbs' => []])
@php($hasIcon = isset($icon) && $icon->isNotEmpty())
<section class="relative isolate overflow-hidden bg-black-deep text-white lg:flex lg:min-h-[max(75vh,34rem)] lg:items-center">
    @if ($image)
        <img src="https://images.unsplash.com/photo-{{ $image }}?auto=format&fit=crop&w=1920&h=800&q=75" alt="" class="absolute inset-0 -z-30 size-full object-cover" fetchpriority="high" referrerpolicy="no-referrer" aria-hidden="true">
    @endif
    {{-- Tanpa ikon (atau di ponsel): foto ditutup gelap merata dengan gradasi dari kiri. --}}
    <span @class(['absolute inset-0 -z-20 bg-black-deep/80', 'lg:hidden' => $hasIcon]) aria-hidden="true"></span>
    <span @class(['absolute inset-0 -z-10 bg-linear-to-r from-black-deep via-black-deep/70 to-black-deep/30', 'lg:hidden' => $hasIcon]) aria-hidden="true"></span>
    {{-- Gradient atas agar navbar transparan tetap terbaca. --}}
    <span class="absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-black-deep/70 to-transparent" aria-hidden="true"></span>

    @if ($hasIcon)
        {{-- Desktop: foto hanya terbuka di dalam lingkaran; di luarnya ditutup bayangan gelap selebar layar (box-shadow, dipotong overflow-hidden), sehingga batas gelap dan foto berupa busur cekung.
             Lingkaran sedikit lebih tinggi dari hero (atas dan bawah terpotong) dengan pusat di 88% lebar (agar alas ikon tidak terpotong tepi layar). Busur putih = tepi lingkaran, busur merah tipis di luarnya sebagai aksen; ikon di atas cakram putih dengan lingkaran halo tipis. --}}
        <div class="pointer-events-none absolute left-[88%] top-1/2 -z-10 hidden aspect-square h-[125%] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white shadow-[0_0_0_100vmax_color-mix(in_srgb,var(--color-black-deep)_82%,transparent)] lg:flex" aria-hidden="true">
            <span class="absolute -inset-4 rounded-full border-2 border-brand/80"></span>
            <span class="flex size-56 items-center justify-center rounded-full bg-white text-black-soft shadow-2xl ring-[14px] ring-white/25 xl:size-64">
                <span class="flex size-28 xl:size-32">{{ $icon }}</span>
            </span>
        </div>
    @endif

    <div class="container-site w-full pb-16 pt-32 sm:pb-20 sm:pt-40 lg:pb-20 lg:pt-36">
        @if ($crumbs)
            <nav aria-label="Jejak halaman" class="mb-8">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold uppercase tracking-wider text-white/60">
                    @foreach ($crumbs as $crumb)
                        <li class="flex items-center gap-2">
                            @if ($crumb['href'])
                                <a href="{{ $crumb['href'] }}" class="transition-colors hover:text-white">{{ $crumb['label'] }}</a>
                                <span aria-hidden="true">/</span>
                            @else
                                <span class="text-white" aria-current="page">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <div class="max-w-3xl border-l-4 border-brand-bright pl-5 sm:pl-8 lg:max-w-2xl">
            @if ($hasIcon)
                <span class="mb-5 flex size-14 items-center justify-center rounded-full bg-white p-3 text-black-soft ring-4 ring-white/25 lg:hidden" aria-hidden="true">{{ $icon }}</span>
            @endif
            @if ($eyebrow)
                <p class="text-sm font-bold uppercase tracking-widest text-brand-bright">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-4 text-3xl font-black leading-[1.1] tracking-tight sm:text-4xl lg:text-5xl">{{ $title }}</h1>
            @if (trim($slot))
                <p class="mt-5 text-base leading-8 text-white/80 sm:text-lg">{{ $slot }}</p>
            @endif
        </div>
    </div>
    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-brand" aria-hidden="true"></div>
</section>
