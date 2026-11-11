{{-- Props: service (array dengan name, icon, image, description; image null = latar polos). Kartu foto penuh di dalam web.service-carousel.
     Saat hover/fokus (varian reveal di app.css) kartu terbagi: panel putih setinggi 36% kartu dan area foto menyusut ke atasnya.
     Jaga angka 36% tetap sama di tinggi panel dan di bottom area foto. --}}
@props(['service'])
<li data-carousel-item
    class="group/card relative isolate aspect-[2/3] shrink-0 basis-[260px] overflow-hidden bg-black-soft sm:basis-[300px] lg:basis-[320px]">
    {{-- Sementara menuju bagian kontak; ganti ke halaman detail layanan bila sudah tersedia. --}}
    <a href="/#kontak" draggable="false" class="absolute inset-0 block">
        {{-- Area foto: penuh saat diam, menyusut ke atas panel saat hover. Foto di-crop ulang (object-cover) mengikuti ukurannya. --}}
        <div class="absolute inset-x-0 top-0 bottom-0 overflow-hidden transition-[bottom] duration-500 ease-out reveal:bottom-[36%]">
            @if ($service['image'])
                {{-- Foto PLACEHOLDER (Picsum). Ganti dengan foto resmi perusahaan bila tersedia. --}}
                <img src="https://picsum.photos/id/{{ $service['image'] }}/640/960" alt="" width="640" height="960" loading="lazy" draggable="false" referrerpolicy="no-referrer"
                     class="size-full select-none object-cover transition-transform duration-500 ease-out reveal:scale-105">
            @else
                {{-- Latar polos sampai foto resmi tersedia. --}}
                <span class="block size-full bg-linear-to-br from-[#26262a] via-black-soft to-black-deep" aria-hidden="true"></span>
            @endif
        </div>

        {{-- Panel: transparan dengan gradasi gelap saat diam (ikon dan judul di bawah), putih berisi deskripsi saat hover.
             isolate agar lapisan latarnya (-z-10) tetap di atas foto. --}}
        <div class="absolute inset-x-0 bottom-0 isolate h-[11.25rem] px-6 pb-3 pt-[7.25rem] text-center transition-[height,padding] duration-500 ease-out reveal:h-[36%] reveal:pt-8">
            <span class="absolute inset-0 -z-10 bg-linear-to-t from-black-deep/90 via-black-deep/55 to-transparent transition-opacity duration-500 reveal:opacity-0" aria-hidden="true"></span>
            <span class="absolute inset-0 -z-10 bg-white opacity-0 transition-opacity duration-500 reveal:opacity-100" aria-hidden="true"></span>
            {{-- Garis merah di tepi atas panel putih. --}}
            <span class="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-brand transition-transform duration-500 reveal:scale-x-100" aria-hidden="true"></span>

            {{-- Lingkaran ikon: di atas judul saat diam; saat hover naik dan bertumpu di garis batas foto dan panel putih. --}}
            <span class="absolute left-1/2 top-14 flex size-12 -translate-x-1/2 items-center justify-center rounded-full border border-white/30 bg-black-deep/40 backdrop-blur-sm transition-[top,translate,background-color,border-color] duration-500 ease-out reveal:top-0 reveal:-translate-y-1/2 reveal:border-white reveal:bg-black-soft">
                <x-web.service-icon :name="$service['icon']" accent="stroke-brand-bright" class="size-7 text-white" />
            </span>
            {{-- Ruang judul untuk dua baris, judul di tengahnya. --}}
            <div class="flex min-h-11 items-center justify-center">
                <h3 class="font-display text-base font-bold leading-snug tracking-tight text-white transition-colors duration-500 reveal:text-black-soft">{{ $service['name'] }}</h3>
            </div>
            {{-- Deskripsi: tersembunyi saat diam, muncul saat panel putih terbuka (maks. dua baris). --}}
            <p class="line-clamp-2 pt-1 text-[0.8125rem] leading-5 text-muted opacity-0 transition-opacity duration-500 reveal:opacity-100">{{ $service['description'] }}</p>
        </div>
    </a>
</li>
