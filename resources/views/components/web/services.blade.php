{{-- Props: services (daftar layanan; tiap item berisi name, icon, image) --}}
@props(['services'])
<section id="layanan" class="scroll-mt-20 overflow-hidden bg-gray-tint py-20 lg:py-28">
    <div class="container-site">
        <x-web.section-title eyebrow="Layanan Kami" title="Produk dan Layanan" center>
            Beragam kategori yang kami sediakan untuk memenuhi kebutuhan Anda.
        </x-web.section-title>

        {{-- Menuju halaman daftar layanan (belum tersedia). --}}
        <div data-reveal class="mt-8 flex justify-center">
            <x-ui.button href="/layanan" variant="dark">
                Lihat Semua
                <x-ui.icon name="arrow-right" class="size-4" />
            </x-ui.button>
        </div>
    </div>

    {{-- Carousel selebar layar (tanpa padding kontainer). --}}
    <div data-reveal class="mt-12">
        <x-web.service-carousel :services="$services" />
    </div>
</section>
