{{-- Props: content (PageContentSet halaman Beranda), serviceCount (jumlah kategori produk & layanan). Profil singkat; profil lengkap ada di halaman Tentang Kami. --}}
@props(['content', 'serviceCount'])
<section{{ $content->mark('about') }} id="tentang" class="scroll-mt-20 bg-white py-16 sm:py-20 lg:py-28">
    <div class="container-site grid items-center gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-7">
            <x-web.section-title :eyebrow="$content('about_eyebrow')" :title="$content->rich('about_title')" />
            <div data-reveal class="mt-8 max-w-2xl space-y-5 text-justify text-base leading-8 text-muted">
                <p>{{ $content('about_paragraph_1') }}</p>
                @if (filled($content('about_paragraph_2')))
                    <p>{{ $content('about_paragraph_2') }}</p>
                @endif
            </div>
            <div data-reveal class="mt-9">
                <x-ui.button href="/tentang-kami" variant="dark">
                    {{ $content('about_button') }}
                    <x-ui.icon name="arrow-right" class="size-4" />
                </x-ui.button>
            </div>
        </div>

        <div class="relative isolate overflow-hidden border border-black-soft/10 bg-gray-tint p-7 sm:p-10 lg:col-span-5 lg:p-12">
            <svg class="absolute -bottom-16 -right-16 -z-10 size-64" viewBox="0 0 400 400" fill="none" stroke="#0b0b0c" stroke-width="1.25" aria-hidden="true">
                <rect x="130" y="130" width="140" height="140" stroke-opacity=".06"/>
                <rect x="65" y="65" width="270" height="270" stroke="#d90000" stroke-opacity=".25"/>
                <rect x="5" y="5" width="390" height="390" stroke-opacity=".04"/>
            </svg>
            <span class="absolute left-0 top-0 h-1 w-16 bg-brand" aria-hidden="true"></span>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-muted">{{ $content('about_stat_label') }}</p>
            <p class="mt-5 font-display text-7xl sm:text-8xl font-black leading-none tracking-tight text-black-soft">{{ $serviceCount }}<span class="text-brand">.</span></p>
            <p class="mt-8 max-w-xs border-t border-black-soft/10 pt-6 text-sm leading-6 text-muted">{{ $content('about_stat_description') }}</p>
            <a href="/#layanan" class="group mt-5 inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
                {{ $content('about_stat_link') }}
                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
            </a>
        </div>
    </div>
</section>
