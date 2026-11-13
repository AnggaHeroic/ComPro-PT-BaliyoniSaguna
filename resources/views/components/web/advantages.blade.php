{{-- Props: advantages (daftar keunggulan; tiap item berisi icon, title, text) --}}
@props(['advantages'])
<section id="mengapa-kami" class="relative isolate scroll-mt-20 overflow-hidden bg-black-deep py-20 text-white lg:py-28">
    {{-- Aksen: kotak tipis bertumpuk, dekoratif saja. --}}
    <svg class="absolute -left-40 top-1/2 -z-10 hidden size-[28rem] -translate-y-1/2 lg:block" viewBox="0 0 400 400" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
        <rect x="140" y="140" width="120" height="120" stroke-opacity=".05"/>
        <rect x="95" y="95" width="210" height="210" stroke-opacity=".05"/>
        <rect x="50" y="50" width="300" height="300" stroke="#ff0000" stroke-opacity=".2"/>
    </svg>
    <div class="container-site">
        <x-web.section-title eyebrow="Mengapa Kami" title="Solusi Terpadu untuk Bisnis dan Instansi" invert center width="max-w-3xl">
            <span class="block">PT. Baliyoni Saguna menghadirkan ekosistem digital terpadu yang menghubungkan semua layanan dalam satu genggaman. Dengan teknologi Business Intelligence dan Single Sign-On (SSO), pengalaman belanja Anda menjadi lebih mudah, cepat, dan aman.</span>
        </x-web.section-title>

        {{-- Tiga kolom di baris pertama; dua sisanya di baris kedua dan berada di tengah (lg:grid-cols-6, tiap item 2 kolom). Isi tiap item rata tengah. --}}
        <div data-reveal-stagger class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-6 lg:gap-x-8 lg:gap-y-12">
            @foreach ($advantages as $item)
                <div @class(['flex flex-col items-center border-t border-white/15 pt-6 text-center lg:col-span-2', 'lg:col-start-2' => $loop->iteration === 4])>
                    <x-web.advantage-icon :name="$item['icon']" accent="stroke-brand-bright" class="size-14 text-white" />
                    <h3 class="mt-5 font-display text-xl font-bold leading-snug">{{ $item['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
