{{-- Props: company (profil; memakai name dan city), serviceCount (jumlah kategori produk & layanan). Profil singkat; profil lengkap ada di halaman Tentang Kami. --}}
@props(['company', 'serviceCount'])
<section id="tentang" class="scroll-mt-20 bg-white py-20 lg:py-28">
    <div class="container-site grid items-center gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-7">
            <x-web.section-title eyebrow="Profil Singkat" title="Mengenal PT. Baliyoni Saguna" />
            <div class="mt-8 max-w-2xl space-y-5 text-base leading-8 text-muted">
                <p>{{ $company['name'] }} adalah perusahaan induk dari berbagai holding dan outlet Baliyoni Group yang berkedudukan di {{ $company['city'] }}.</p>
                <p>Dengan semboyan <strong class="font-semibold text-black-soft">One Stop Solution</strong>, kami menghadirkan beragam produk dan layanan dalam satu pintu.</p>
            </div>
            <div class="mt-9">
                <x-ui.button href="/tentang-kami" variant="dark">
                    Selengkapnya
                    <x-ui.icon name="arrow-right" class="size-4" />
                </x-ui.button>
            </div>
        </div>

        <div class="relative isolate overflow-hidden border border-black-soft/10 bg-gray-tint p-10 lg:col-span-5 lg:p-12">
            <svg class="absolute -bottom-16 -right-16 -z-10 size-64" viewBox="0 0 400 400" fill="none" stroke="#0b0b0c" stroke-width="1.25" aria-hidden="true">
                <rect x="130" y="130" width="140" height="140" stroke-opacity=".06"/>
                <rect x="65" y="65" width="270" height="270" stroke="#d90000" stroke-opacity=".25"/>
                <rect x="5" y="5" width="390" height="390" stroke-opacity=".04"/>
            </svg>
            <span class="absolute left-0 top-0 h-1 w-16 bg-brand" aria-hidden="true"></span>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-muted">Kategori Produk &amp; Layanan</p>
            <p class="mt-5 font-display text-8xl font-black leading-none tracking-tight text-black-soft">{{ $serviceCount }}<span class="text-brand">.</span></p>
            <p class="mt-8 max-w-xs border-t border-black-soft/10 pt-6 text-sm leading-6 text-muted">Kebutuhan bisnis, instansi, dan perorangan dalam satu tempat.</p>
            <a href="/#layanan" class="group mt-5 inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
                Lihat semua layanan
                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
            </a>
        </div>
    </div>
</section>
