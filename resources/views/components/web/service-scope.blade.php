{{-- Props: services (daftar kategori layanan; memakai name, icon, description, activities berisi name, detail, summary opsional). Dua bagian berurutan dalam satu pembungkus: Ruang Lingkup (gelap, pemilih kategori) dan Aktivitas Usaha (putih, muncul setelah kategori dipilih; berupa card yang membuka modal detail lewat x-ui.modal). Perilaku ada di resources/js/service-scope.js. --}}
@props(['services'])
<div x-data="serviceScope" @hashchange.window="fromHash()" @scroll.window.passive="onScroll()">
    <section id="ruang-lingkup" class="scroll-mt-20 bg-black-deep py-20 text-white lg:py-28">
        <div class="container-site">
            <x-web.section-title eyebrow="Ruang Lingkup" title="Produk dan Layanan Kami" invert center>
                Beragam kategori yang dapat dipenuhi dalam satu tempat.
            </x-web.section-title>

            <ul class="mt-14 grid gap-px border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($services as $service)
                    <li class="bg-black-deep">
                        {{-- Pemilih kategori: saat disorot, latar berubah putih dengan teks merah (transisi singkat); keadaan terpilih (aria-pressed) sama, ditambah garis merah tipis di kiri. --}}
                        <button type="button" data-slug="{{ Str::slug($service['name']) }}"
                                @click="select('{{ Str::slug($service['name']) }}')"
                                :aria-pressed="active === '{{ Str::slug($service['name']) }}'" aria-pressed="false" aria-controls="aktivitas-usaha"
                                class="group flex h-full w-full items-center gap-4 p-5 text-left transition-colors duration-150 hover:bg-white hover:text-brand aria-pressed:bg-white aria-pressed:text-brand aria-pressed:shadow-[inset_3px_0_0_0_var(--color-brand)]">
                            <x-web.service-icon :name="$service['icon']" accent="stroke-brand" class="size-9 shrink-0 text-white transition-colors duration-150 group-hover:text-black-soft group-aria-pressed:text-black-soft" />
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
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                Pilih salah satu kategori di atas untuk melihat aktivitas usahanya.
            </p>

            <div x-show="active !== null" x-cloak class="py-16 lg:py-24">
            @foreach ($services as $service)
                <div x-show="active === '{{ Str::slug($service['name']) }}'" x-cloak x-transition.opacity.duration.200ms>
                    {{-- Judul kategori: ikon di kiri, label dan nama di sampingnya, deskripsi di bawah. --}}
                    <div class="flex items-center gap-4">
                        <x-web.service-icon :name="$service['icon']" accent="stroke-brand" class="size-12 shrink-0 text-black-soft" />
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">Aktivitas Usaha</p>
                            <h3 class="mt-1 font-display text-2xl font-black leading-tight sm:text-3xl">{{ $service['name'] }}</h3>
                        </div>
                    </div>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-muted sm:text-base">{{ $service['description'] }}</p>

                    {{-- Aktivitas dalam bentuk card; klik card membuka modal berisi detailnya (konten sudah ada di HTML, hanya disembunyikan). --}}
                    <ol class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($service['activities'] as $activity)
                            @php($modal = 'aktivitas-'.Str::slug($service['name']).'-'.$loop->iteration)
                            <li>
                                <button type="button" @click="$dispatch('open-modal', '{{ $modal }}')" aria-haspopup="dialog"
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
                                </button>

                                <x-ui.modal :name="$modal" :eyebrow="$service['name']" :title="$activity['name']">
                                    {{ $activity['detail'] ?? 'Rincian aktivitas ini akan segera dilengkapi.' }}
                                </x-ui.modal>
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
