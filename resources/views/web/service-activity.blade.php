@extends('layouts.public', ['transparentNav' => true, 'title' => $activity['name'], 'description' => $activity['summary'] ?? 'Aktivitas usaha '.$service['name'].' PT Baliyoni Saguna: '.$activity['name'].'.'])

@section('content')
    @php($serviceSlug = Str::slug($service['name']))
    <x-web.detail-header :title="$activity['name']" :eyebrow="$service['name']" :image="$service['image']" :crumbs="[
        ['label' => 'Layanan', 'href' => route('services')],
        ['label' => $service['name'], 'href' => route('services').'#ruang-lingkup-'.$serviceSlug],
        ['label' => $activity['name'], 'href' => null],
    ]">
        <x-slot:icon>
            <x-web.service-icon :name="$service['icon']" accent="stroke-brand-bright" class="size-full" />
        </x-slot:icon>
        {{ $activity['summary'] ?? 'Aktivitas usaha pada kategori '.$service['name'].'.' }}
    </x-web.detail-header>

    <section class="bg-white py-16 lg:py-24">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            {{-- Isi utama: artikel aktivitas (foto, deskripsi, ringkasan; tata letak diadaptasi dari halaman detail aktivitas PT Bali Oase Semesta), lalu navigasi dan aktivitas lain dalam kategori yang sama. --}}
            <div class="lg:col-span-8">
                <article>
                    {{-- Foto PLACEHOLDER sementara (Unsplash). Ganti dengan foto resmi perusahaan bila tersedia. --}}
                    @if ($service['image'])
                        <div class="relative aspect-[16/9] overflow-hidden bg-black-deep" aria-hidden="true">
                            <img src="https://images.unsplash.com/photo-{{ $service['image'] }}?auto=format&fit=crop&w=1200&h=675&q=75" alt="" width="1200" height="675" loading="lazy" referrerpolicy="no-referrer" class="size-full object-cover">
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-linear-to-t from-black-deep/60 to-transparent"></div>
                            <div class="absolute left-0 top-0 h-1 w-24 bg-brand"></div>
                            <div class="absolute right-6 top-6 size-8 border-r-2 border-t-2 border-white/80"></div>
                        </div>
                    @endif

                    <div @class(['border border-black-soft/10 bg-white px-6 pb-8 sm:px-10 sm:pb-10', 'border-t-0' => $service['image'], 'border-t-4 border-t-brand pt-8' => ! $service['image']])>
                        {{-- Ikon bulat menumpang di sambungan foto dan panel. --}}
                        <span @class(['relative z-10 flex size-16 items-center justify-center rounded-full bg-black-soft text-white ring-4 ring-white', '-mt-8' => $service['image']]) aria-hidden="true">
                            <x-web.service-icon :name="$service['icon']" accent="stroke-brand-bright" class="size-9" />
                        </span>

                        {{-- Ringkasan: dua kartu kecil (nama aktivitas dan kategori) tepat di atas deskripsi. --}}
                        <dl class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="border border-black-soft/10 bg-gray-tint px-4 py-3">
                                <dt class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand">Aktivitas Usaha</dt>
                                <dd class="mt-1 text-sm font-semibold leading-6 text-black-soft">{{ $activity['name'] }}</dd>
                            </div>
                            <div class="border border-black-soft/10 bg-gray-tint px-4 py-3">
                                <dt class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand">Kategori Usaha</dt>
                                <dd class="mt-1 text-sm font-semibold leading-6 text-black-soft"><a href="{{ route('services').'#ruang-lingkup-'.$serviceSlug }}" class="transition-colors hover:text-brand">{{ $service['name'] }}</a></dd>
                            </div>
                        </dl>

                        <h2 class="mt-10 text-xs font-bold uppercase tracking-[0.18em] text-brand">Deskripsi Aktivitas Usaha</h2>
                        <div class="mt-4 space-y-5 text-justify text-base leading-8 text-muted">
                            @foreach ($paragraphs as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                </article>

                {{-- Navigasi antar aktivitas dalam kategori; kotak kosong dijaga agar tombol berikutnya tetap di kanan; ditaruh tepat di bawah penjelasan. --}}
                <div class="mt-12 grid gap-4 sm:grid-cols-2">
                    @if ($previous)
                        <a href="{{ route('services.show', ['service' => $serviceSlug, 'activity' => Str::slug($previous['name'])]) }}" class="group border border-black-soft/15 p-5 transition-colors hover:border-brand">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand">Sebelumnya</span>
                            <span class="mt-2 block text-sm font-bold transition-colors group-hover:text-brand">{{ $previous['name'] }}</span>
                        </a>
                    @else
                        <span class="hidden sm:block" aria-hidden="true"></span>
                    @endif
                    @if ($next)
                        <a href="{{ route('services.show', ['service' => $serviceSlug, 'activity' => Str::slug($next['name'])]) }}" class="group border border-black-soft/15 p-5 text-left transition-colors hover:border-brand sm:text-right">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand">Berikutnya</span>
                            <span class="mt-2 block text-sm font-bold transition-colors group-hover:text-brand">{{ $next['name'] }}</span>
                        </a>
                    @endif
                </div>

                <div class="mt-16">
                    <h2 class="font-display text-xl font-black sm:text-2xl">Aktivitas Lain di {{ $service['name'] }}</h2>
                    <ol class="mt-6 divide-y divide-black-soft/10 border-y border-black-soft/10">
                        @foreach ($activities as $item)
                            <li>
                                <a href="{{ route('services.show', ['service' => $serviceSlug, 'activity' => Str::slug($item['name'])]) }}"
                                   @if ($loop->iteration === $number) aria-current="page" @endif
                                   class="group flex items-center gap-4 px-2 py-4 transition-colors hover:bg-gray-tint aria-[current=page]:bg-gray-tint aria-[current=page]:shadow-[inset_3px_0_0_0_var(--color-brand)]">
                                    <span class="w-8 shrink-0 font-display text-sm font-black text-brand">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="flex-1 text-sm font-semibold transition-colors group-hover:text-brand group-aria-[current=page]:text-brand sm:text-base">{{ $item['name'] }}</span>
                                    <x-ui.icon name="arrow-right" class="size-4 text-muted transition-transform group-hover:translate-x-1 group-hover:text-brand" />
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            {{-- Sisi kanan: ajakan menghubungi dan kartu kategori; menempel saat gulir di layar lebar. --}}
            <aside class="space-y-6 lg:col-span-4 lg:sticky lg:top-28 lg:self-start">
                <div class="border border-t-4 border-black-soft/10 border-t-brand bg-white p-6">
                    <h2 class="font-display text-xl font-black leading-tight">Tertarik dengan aktivitas ini?</h2>
                    <p class="mt-3 text-sm leading-7 text-muted">Sampaikan kebutuhan Anda, tim kami siap membantu.</p>
                    <div class="mt-6 flex flex-col gap-3">
                        <x-ui.button :href="route('contact', ['perihal' => $activity['name']])">
                            Tanyakan Aktivitas Ini
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </x-ui.button>
                        @if ($company['mobile_url'])
                            <x-ui.button :href="$company['mobile_url'].'?text='.rawurlencode('Halo, saya ingin bertanya tentang '.$activity['name'].'.')" variant="outline" target="_blank" rel="noopener noreferrer">WhatsApp Kami</x-ui.button>
                        @endif
                    </div>
                </div>

                <div class="border border-black-soft/10 bg-gray-tint p-6">
                    <div class="flex items-center gap-4">
                        <x-web.service-icon :name="$service['icon']" accent="stroke-brand" class="size-10 shrink-0 text-black-soft" />
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Kategori</p>
                            <p class="mt-1 font-display text-base font-bold leading-snug">{{ $service['name'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('services', ['kategori' => $serviceSlug]).'#ruang-lingkup' }}" class="group mt-5 inline-flex items-center gap-2 text-sm font-semibold transition-colors hover:text-brand">
                        Lihat semua kategori
                        <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>
            </aside>
        </div>
    </section>

    <x-web.cta :company="$company" variant="banner" />
@endsection
