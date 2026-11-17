{{-- Props: content (PageContentSet halaman Beranda), services (daftar layanan; tiap item berisi name, icon, image) --}}
@props(['content', 'services'])
<section{{ $content->mark('services') }} id="layanan" class="scroll-mt-20 overflow-hidden bg-gray-tint py-16 sm:py-20 lg:py-28">
    <div class="container-site">
        <x-web.section-title :eyebrow="$content('services_eyebrow')" :title="$content->rich('services_title')" center>
            {{ $content('services_description') }}
        </x-web.section-title>

        {{-- Menuju halaman daftar layanan. --}}
        <div data-reveal class="mt-8 flex justify-center">
            <x-ui.button href="/layanan" variant="dark">
                {{ $content('services_button') }}
                <x-ui.icon name="arrow-right" class="size-4" />
            </x-ui.button>
        </div>
    </div>

    {{-- Carousel selebar layar (tanpa padding kontainer). --}}
    <div data-reveal class="mt-12">
        <x-web.service-carousel :services="$services" />
    </div>
</section>
