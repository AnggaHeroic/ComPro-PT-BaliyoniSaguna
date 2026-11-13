@extends('layouts.public', ['transparentNav' => true, 'title' => 'Galeri', 'description' => 'Galeri foto PT Baliyoni Saguna: kantor, produk, dan layanan kami.'])

@section('content')
    <x-web.hero-aside eyebrow="Galeri" title="Jejak Visual" highlight="Baliyoni Saguna" scrollTo="#galeri-foto" scrollLabel="Gulir ke galeri foto">
        Dokumentasi kantor, produk, dan layanan kami dalam satu galeri.
        <x-slot:aside>
            <x-web.gallery-mosaic :photos="array_column($gallery, 'image')" />
        </x-slot:aside>
    </x-web.hero-aside>

    <section id="galeri-foto" class="scroll-mt-20 bg-white py-20 lg:py-28">
        <div class="container-site">
            <x-web.section-title eyebrow="Galeri Foto" title="Dokumentasi Kami" center>
                Klik foto untuk melihat tampilan yang lebih besar.
            </x-web.section-title>
            <div class="mt-14">
                <x-web.gallery-grid :gallery="$gallery" />
            </div>
        </div>
    </section>
@endsection
