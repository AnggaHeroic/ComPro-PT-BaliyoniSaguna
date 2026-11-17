{{-- Props: gallery (daftar foto; memakai title, description, image = URL foto), spans (pola ukuran kotak yang diulang per foto; hanya berlaku di layar lebar). Kisi bento: di layar lebar 4 kolom dengan kotak besar, tinggi, dan lebar yang berselang-seling; di bawah itu 2 kolom seragam. Judul selalu tampil di foto; saat foto disorot, panel keterangan naik dan menampilkan deskripsi. Klik foto membuka pratinjau (lightbox, tata letak dan logika zoom diadaptasi dari PT Putra Maju Sukses); perilaku ada di resources/js/gallery-lightbox.js. --}}
{{-- Pola 6 foto mengisi blok 4 kolom x 3 baris tanpa lubang: besar (2x2), kecil, tinggi (1x2), kecil, lebar (2x1), lebar. Blok kedua dicerminkan agar tidak terlihat berulang. Setiap kelipatan 6 foto membentuk blok utuh. --}}
@props(['gallery', 'spans' => \App\Models\GalleryPhoto::BENTO_SPANS])
<div x-data="galleryLightbox(@js(array_map(fn (array $photo) => [
    'title' => $photo['title'],
    'description' => $photo['description'] ?? null,
    'src' => $photo['image'],
], $gallery)))"
     @keydown.escape.window="close()" @keydown.arrow-right.window="isOpen && next()" @keydown.arrow-left.window="isOpen && previous()">
    <ul data-reveal-stagger class="grid grid-cols-2 gap-3 sm:gap-4 lg:auto-rows-[13rem] lg:grid-flow-dense lg:grid-cols-4">
        @foreach ($gallery as $photo)
            <li class="min-h-0 {{ $spans[$loop->index % count($spans)] }}">
                <button type="button" @click="open({{ $loop->index }})" aria-haspopup="dialog" aria-label="Lihat foto: {{ $photo['title'] }}"
                        class="group relative block aspect-[4/3] size-full overflow-hidden bg-black-deep text-left lg:aspect-auto">
                    <img src="{{ $photo['image'] }}" alt="{{ $photo['title'] }}" loading="lazy" referrerpolicy="no-referrer"
                         class="size-full object-cover transition-transform duration-500 group-hover:scale-105">
                    {{-- Gradasi dasar agar judul terbaca; menebal saat disorot. --}}
                    <span class="absolute inset-0 bg-linear-to-t from-black-deep/80 via-transparent to-transparent transition-colors duration-300 group-hover:bg-black-deep/40 group-focus-visible:bg-black-deep/40" aria-hidden="true"></span>
                    {{-- Panel keterangan: judul selalu terlihat; saat disorot atau difokus (keyboard) panel naik karena deskripsi terbuka di bawah judul. --}}
                    <span class="absolute inset-x-0 bottom-0 px-4 pb-4 text-white">
                        <span class="block text-sm font-bold leading-snug sm:text-base">{{ $photo['title'] }}</span>
                        @if (! empty($photo['description']))
                            <span class="grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 group-hover:grid-rows-[1fr] group-focus-visible:grid-rows-[1fr]">
                                <span class="block min-h-0 translate-y-3 overflow-hidden text-xs leading-5 text-white/85 opacity-0 transition-all duration-300 group-hover:mt-1.5 group-hover:translate-y-0 group-hover:opacity-100 group-focus-visible:mt-1.5 group-focus-visible:translate-y-0 group-focus-visible:opacity-100">{{ $photo['description'] }}</span>
                            </span>
                        @endif
                    </span>
                    <span class="absolute inset-x-0 bottom-0 h-0.5 origin-left scale-x-0 bg-brand transition-transform duration-500 group-hover:scale-x-100" aria-hidden="true"></span>
                </button>
            </li>
        @endforeach
    </ul>

    <x-web.gallery-lightbox />
</div>
