@extends('layouts.public', ['transparentNav' => true, 'title' => 'Galeri', 'description' => $content('seo_description')])

@section('content')
    <x-web.hero-aside :data-cms-section="$content->sectionKey('hero')" :eyebrow="$content('hero_eyebrow')" :title="$content->rich('hero_title', 'text-brand-bright')" scrollTo="#galeri-foto" scrollLabel="Gulir ke galeri foto">
        {{ $content('hero_description') }}
        <x-slot:aside>
            <x-web.gallery-mosaic :photos="array_column($gallery, 'image')" />
        </x-slot:aside>
    </x-web.hero-aside>

    <section{{ $content->mark('photos') }} id="galeri-foto" class="scroll-mt-20 bg-white py-16 sm:py-20 lg:py-28">
        <div class="container-site">
            <x-web.section-title :eyebrow="$content('photos_eyebrow')" :title="$content->rich('photos_title')" center>
                {{ $content('photos_description') }}
            </x-web.section-title>
            <div class="mt-10 sm:mt-14">
                <x-web.gallery-grid :gallery="$gallery" />
            </div>
        </div>
    </section>
@endsection
