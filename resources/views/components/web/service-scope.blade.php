{{-- Props: content (PageContentSet halaman Layanan; judul dan petunjuk diatur admin), services (daftar kategori layanan; memakai name, icon, description, activities berisi name, summary opsional (detail dipakai di halaman detail)). Dua bagian berurutan dalam satu pembungkus: Ruang Lingkup (gelap, pemilih kategori) dan Aktivitas Usaha (putih, muncul setelah kategori dipilih; berupa card yang menuju halaman detail aktivitas). Perilaku ada di resources/js/service-scope.js. --}}
@props(['content', 'services'])
<div{{ $content->mark('scope') }} x-data="serviceScope" @hashchange.window="fromHash()" @scroll.window.passive="onScroll()">
    <section id="ruang-lingkup" class="scroll-mt-20 bg-black-deep py-16 text-white sm:py-20 lg:py-28">
        <div class="container-site">
            <x-web.section-title :eyebrow="$content('scope_eyebrow')" :title="$content->rich('scope_title', 'text-brand-bright')" invert center>
                {{ $content('scope_description') }}
            </x-web.section-title>

            <ul data-reveal-stagger class="mt-10 grid grid-cols-2 pl-px pt-px sm:mt-14 lg:grid-cols-4">
                @foreach ($services as $service)
                    <li class="-ml-px -mt-px border border-white/10 bg-black-deep">
                        {{-- Pemilih kategori: saat disorot, latar berubah putih dengan teks merah (transisi singkat); keadaan terpilih (aria-pressed) sama, ditambah garis merah tipis di kiri. --}}
                        <button type="button" data-slug="{{ $service['slug'] }}"
                                @click="select('{{ $service['slug'] }}')"
                                :aria-pressed="active === '{{ $service['slug'] }}'" aria-pressed="false" aria-controls="aktivitas-usaha"
                                class="group flex h-full w-full flex-col items-start gap-3 p-4 text-left sm:flex-row sm:items-center sm:gap-4 sm:p-5 transition-colors duration-150 hover:bg-white hover:text-brand aria-pressed:bg-white aria-pressed:text-brand aria-pressed:shadow-[inset_3px_0_0_0_var(--color-brand)]">
                            <x-web.service-icon :name="$service['icon']" :image="$service['icon_image']" accent="stroke-brand" class="size-8 shrink-0 text-white transition-colors sm:size-9 duration-150 group-hover:text-black-soft group-aria-pressed:text-black-soft" />
                            <span class="text-sm font-semibold leading-snug">{{ $service['name'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

        </div>
    </section>

    {{-- Bagian berlatar putih di bawah Ruang Lingkup (di antara grid kategori dan CTA). Belum ada kategori dipilih: berisi catatan pilihan. Setelah dipilih: daftar aktivitas kategori itu. --}}
    <section id="aktivitas-usaha" x-ref="panel" class="scroll-mt-20 border-t-2 border-brand bg-white text-black-soft">
        <div class="container-site">
            <p x-show="active === null" class="flex items-center justify-center gap-2 py-10 text-center text-sm text-muted">
                {{-- Chevron melompat (seperti tombol gulir di hero) sebagai petunjuk arah; berhenti bila pengguna memilih mengurangi gerakan. --}}
                <svg class="size-4 shrink-0 motion-safe:animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                {{ $content('scope_hint') }}
            </p>

            <div x-show="active !== null" x-cloak class="py-12 sm:py-16 lg:py-24">
            @foreach ($services as $service)
                <div x-show="active === '{{ $service['slug'] }}'" x-cloak x-transition.opacity.duration.200ms>
                    {{-- Judul kategori: ikon di kiri, label dan nama di sampingnya, deskripsi di bawah. --}}
                    <div class="flex items-center gap-4">
                        <x-web.service-icon :name="$service['icon']" :image="$service['icon_image']" accent="stroke-brand" class="size-12 shrink-0 text-black-soft" />
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">Aktivitas Usaha</p>
                            <h3 class="mt-1 font-display text-2xl font-black leading-tight sm:text-3xl">{{ $service['name'] }}</h3>
                        </div>
                    </div>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-muted sm:text-base">{{ $service['description'] }}</p>

                    {{-- Aktivitas dalam bentuk card; klik card menuju halaman detail aktivitasnya. --}}
                    <ol class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($service['activities'] as $activity)
                            <li>
                                <a href="{{ route('services.show', ['service' => $service['slug'], 'activity' => $activity['slug']]) }}"
                                   class="group flex h-full w-full flex-col border border-black-soft/15 bg-white p-6 text-left transition-colors duration-150 hover:border-brand">
                                    <span class="font-display text-sm font-black text-brand">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="mt-3 font-display text-base font-bold leading-snug transition-colors duration-150 group-hover:text-brand sm:text-lg">{{ $activity['name'] }}</span>
                                    @if (! empty($activity['summary']))
                                        <span class="mt-3 line-clamp-2 text-sm leading-6 text-muted">{{ $activity['summary'] }}</span>
                                    @endif
                                    <span class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-black-soft transition-colors duration-150 group-hover:text-brand">
                                        Lihat detail
                                        <x-ui.icon name="arrow-right" class="size-4 transition-transform duration-150 group-hover:translate-x-1" />
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endforeach


            {{-- Tombol kembali ke Ruang Lingkup: melayang tetap di kanan dasar layar (juga saat melewati footer) setelah bagian aktivitas dicapai; semi transparan gelap seperti tombol gulir di hero agar terbaca di atas konten putih maupun footer. --}}
            <div x-show="showBack" x-cloak x-transition.opacity.duration.200ms class="pointer-events-none fixed inset-x-0 bottom-6 z-40 flex justify-end pr-4 sm:pr-6">
                <button type="button" @click="backToScope()" aria-label="Kembali ke Ruang Lingkup" title="Kembali ke Ruang Lingkup"
                        class="pointer-events-auto flex size-10 items-center justify-center rounded-full border border-white/40 bg-black-deep/60 text-white shadow-sm backdrop-blur-sm transition-colors hover:border-brand hover:bg-brand">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                </button>
            </div>
            </div>
        </div>
    </section>
</div>
