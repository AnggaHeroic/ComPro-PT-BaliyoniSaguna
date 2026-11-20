{{-- Props: company (profil), navigation (daftar menu), services (kategori untuk mega-menu dan sidebar), transparent (navbar transparan di atas hero) --}}
@props(['company', 'navigation', 'services', 'transparent' => false])
{{-- Perilaku (scroll, mega-menu, sidebar mobile) ada di resources/js/navbar.js. Header menjadi putih setelah scroll atau saat mega-menu terbuka. --}}
<header x-data="navbar" data-site-header
        :data-scrolled="String(solid)" data-scrolled="false"
        @scroll.window.passive="onScroll()"
        @service-selected.window="activeService = $event.detail"
        @resize.window="onResize()"
        @keydown.escape.window="closeAll()"
        @click.outside="closeAll()"
        @focusin.window="if (! $root.contains($event.target)) closeAll()"
        @class([
            'group z-50 border-t-4 border-b transition-[background-color,border-color,box-shadow] duration-300',
            'data-[scrolled=true]:border-black-soft data-[scrolled=true]:border-b-black-soft/10 data-[scrolled=true]:bg-white data-[scrolled=true]:shadow-[0_2px_12px_rgb(0_0_0/0.08)]',
            'fixed inset-x-0 top-0 border-transparent bg-transparent' => $transparent,
            'sticky top-0 border-black-soft border-b-black-soft/10 bg-white' => ! $transparent,
        ])>
    <div class="container-site relative flex h-[4.25rem] items-center justify-between gap-4 lg:h-20 lg:gap-6">
        <div class="flex min-w-0 items-center gap-1 lg:gap-0">
            {{-- Hamburger (mobile) di kiri logo, tanpa border; membuka sidebar navigasi. Margin negatif menyejajarkan garis ikon dengan tepi konten. --}}
            <button type="button" aria-controls="menu-mobile" @click="menu = true" :aria-expanded="menu" aria-expanded="false"
                    @class([
                        '-ml-2.5 inline-flex size-11 shrink-0 items-center justify-center rounded-sm transition-colors lg:hidden',
                        'text-white hover:bg-white/10 group-data-[scrolled=true]:text-black-soft group-data-[scrolled=true]:hover:bg-gray-tint' => $transparent,
                        'text-black-soft hover:bg-gray-tint' => ! $transparent,
                    ])>
                <span class="sr-only">Buka menu navigasi</span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
            </button>

            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5 sm:gap-3" aria-label="{{ $company['name'] }} — Beranda">
                <x-web.logo size="size-10 sm:size-11 lg:size-12" />
                <x-web.wordmark :tone="$transparent ? 'auto' : 'dark'" />
            </a>
        </div>

        <nav class="hidden items-center gap-9 lg:flex" aria-label="Navigasi utama">
            @foreach ($navigation as $item)
                @if ($item['mega'] ?? false)
                    {{-- Klik langsung menuju halaman layanan; di layar sentuh (tanpa hover) ketukan membuka mega-menu. --}}
                    <x-web.nav-link :href="$item['href']" :active="$item['match'] && request()->routeIs($item['match'])" :transparent="$transparent" aria-controls="mega-layanan"
                                    class="group/mega inline-flex items-center gap-1.5 after:hidden aria-expanded:text-black-soft"
                                    x-ref="megaTrigger" ::aria-expanded="mega"
                                    @click="if (! canHover()) { $event.preventDefault(); toggleMega() }" @mouseenter="hoverMega()" @mouseleave="leaveMega()">
                        {{-- Garis indikator dipindah ke teks agar tidak memanjang sampai chevron (offset -bottom-2.5 = padding link + posisi garis menu lain). --}}
                        <span @class([
                            'relative after:absolute after:inset-x-0 after:-bottom-2.5 after:h-0.5 after:origin-left after:bg-brand-bright after:transition-transform group-hover/mega:after:scale-x-100 group-aria-expanded/mega:after:scale-x-100',
                            'after:scale-x-100' => $item['match'] && request()->routeIs($item['match']),
                            'after:scale-x-0' => ! ($item['match'] && request()->routeIs($item['match'])),
                        ])>{{ $item['label'] }}</span>
                        <svg class="size-3.5 transition-transform in-aria-expanded:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </x-web.nav-link>
                @else
                    <x-web.nav-link :href="$item['href']" :active="$item['match'] && request()->routeIs($item['match'])" :transparent="$transparent">
                        {{ $item['label'] }}
                    </x-web.nav-link>
                @endif
            @endforeach
        </nav>

        {{-- Merah di atas hero transparan, hitam setelah scroll; selain itu hitam. Di ponsel disembunyikan (tombolnya ada di sidebar). --}}
        <x-ui.button :href="route('contact')" :variant="$transparent ? 'primary' : 'dark'"
                     :class="'max-lg:!hidden shrink-0 !py-2.5 uppercase tracking-wider'.($transparent ? ' group-data-[scrolled=true]:bg-black-soft group-data-[scrolled=true]:hover:bg-brand' : '')">Kontak Kami</x-ui.button>

        {{-- Tombol kecil kembali ke dashboard (hanya saat admin login). Absolut di ruang kosong tepat di kanan tombol Kontak Kami (padding container), jadi tidak menggeser menu apa pun. Di ponsel ada di sidebar. --}}
        @auth
            <a href="{{ route('admin.dashboard') }}" title="Kembali ke Dashboard Admin" aria-label="Kembali ke Dashboard Admin"
               @class([
                   'absolute left-[calc(100%-2rem+0.25rem)] top-1/2 hidden h-8 w-7 -translate-y-1/2 items-center justify-center gap-1.5 rounded-sm text-xs font-semibold transition-colors lg:inline-flex min-[1280px]:left-[calc(100%-2rem+0.875rem)] 2xl:left-[calc(100%-2rem+1.25rem)] 2xl:w-auto 2xl:px-2.5',
                   'bg-white text-black-soft hover:bg-gray-tint group-data-[scrolled=true]:bg-black-soft group-data-[scrolled=true]:text-white group-data-[scrolled=true]:hover:bg-brand' => $transparent,
                   'bg-black-soft text-white hover:bg-brand' => ! $transparent,
               ])>
                <x-ui.icon name="dashboard" class="size-3.5" />
                <span class="hidden 2xl:inline">Admin</span>
            </a>
        @endauth
    </div>

    {{-- Mega-menu desktop: 4 kolom tetap, terisi per kolom (16 item = 4 baris; lebih dari 16 menambah baris). --}}
    <div id="mega-layanan" x-show="mega" x-cloak x-transition.opacity.duration.150ms
         @mouseenter="hoverMega()" @mouseleave="leaveMega()" @click="if ($event.target.closest('a')) mega = false"
         class="absolute inset-x-0 top-full hidden max-h-[calc(100dvh-5rem)] overflow-y-auto border-b border-t border-black-soft/10 border-b-brand bg-white shadow-[0_16px_32px_rgb(0_0_0/0.10)] lg:block">
        <div class="container-site grid grid-cols-[15rem_1fr] gap-10 py-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">Layanan Kami</p>
                <span class="mt-3 block h-0.5 w-8 bg-brand" aria-hidden="true"></span>
                <p class="mt-4 text-sm leading-relaxed text-muted">Produk dan layanan yang kami sediakan untuk kebutuhan bisnis, instansi, dan perorangan.</p>
                <a href="{{ route('services') }}" class="group mt-6 inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
                    Lihat semua layanan
                    <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                </a>
            </div>
            <ul class="grid auto-rows-min grid-flow-col grid-cols-4 gap-x-6 gap-y-1 [grid-template-rows:repeat(var(--rows),auto)]"
                style="--rows: {{ max(4, (int) ceil(count($services) / 4)) }}">
                @foreach ($services as $service)
                    <li>
                        <a href="{{ route('services') }}#ruang-lingkup-{{ $service['slug'] }}" :aria-current="activeService === '{{ $service['slug'] }}' ? 'true' : false" class="aria-[current=true]:border-brand aria-[current=true]:bg-gray-tint aria-[current=true]:text-brand flex items-center gap-3 border-l-2 border-transparent py-2.5 pl-3 text-[0.9rem] leading-snug text-black-soft transition-colors hover:border-brand hover:text-brand">
                            {{ $service['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Sidebar mobile: panel gelap meluncur dari kiri di atas latar redup; klik latar, tombol tutup, atau Esc menutupnya. Halaman di belakangnya dikunci agar tidak ikut tergulir. --}}
    <div x-show="menu" x-cloak @click="menu = false" aria-hidden="true"
         x-transition:enter="transition-opacity duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black-deep/70 backdrop-blur-[2px] lg:hidden"></div>

    <nav id="menu-mobile" x-show="menu" x-cloak aria-label="Navigasi seluler"
         x-effect="document.body.classList.toggle('overflow-hidden', menu)"
         @click="if ($event.target.closest('a')) menu = false"
         x-transition:enter="transition-transform duration-300 ease-out" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 isolate z-50 flex w-[85%] max-w-sm flex-col overflow-hidden border-t-4 border-brand bg-black-soft text-white shadow-[8px_0_32px_rgb(0_0_0/0.4)] lg:hidden">
        {{-- Aksen kotak tipis bertumpuk (selaras hero Beranda), dekoratif saja. --}}
        <svg class="pointer-events-none absolute -bottom-24 -right-24 -z-10 size-80" viewBox="0 0 400 400" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
            <rect x="140" y="140" width="120" height="120" stroke-opacity=".08"/>
            <rect x="95" y="95" width="210" height="210" stroke="#ff0000" stroke-opacity=".3"/>
            <rect x="50" y="50" width="300" height="300" stroke-opacity=".06"/>
        </svg>
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 pl-5 pr-2">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5" aria-label="{{ $company['name'] }} — Beranda">
                <x-web.logo size="size-9" />
                <x-web.wordmark tone="light" class="!text-base" />
            </a>
            <button type="button" @click="menu = false" class="inline-flex size-11 shrink-0 items-center justify-center text-white/70 transition-colors hover:text-white">
                <span class="sr-only">Tutup menu navigasi</span>
                <x-ui.icon name="close" class="size-6" />
            </button>
        </div>

        <p class="shrink-0 px-5 pt-7 text-[11px] font-bold uppercase tracking-[0.25em] text-brand-bright">Menu</p>
        <ul class="flex-1 overflow-y-auto overscroll-contain pb-4 pt-3">
            @foreach ($navigation as $item)
                @php($current = $item['match'] && request()->routeIs($item['match']))
                <li class="border-b border-white/10" @if ($item['mega'] ?? false) x-data="{ open: {{ $current ? 'true' : 'false' }} }" @endif>
                    @if ($item['mega'] ?? false)
                        <div class="flex items-center justify-between">
                            <a href="{{ $item['href'] }}" @if ($current) aria-current="page" @endif
                               class="group flex flex-1 items-center border-l-2 border-transparent py-4 pl-5 font-display text-lg font-bold tracking-tight text-white/80 transition-colors hover:text-white aria-[current=page]:border-brand-bright aria-[current=page]:bg-white/[0.06] aria-[current=page]:text-white">
                                <span>{{ $item['label'] }}</span>
                            </a>
                            <button type="button" aria-controls="sub-layanan" @click="open = ! open" :aria-expanded="open" aria-expanded="false"
                                    class="mr-2 inline-flex size-11 items-center justify-center text-white/60 transition-colors hover:text-white aria-expanded:text-brand-bright">
                                <span class="sr-only">Tampilkan kategori layanan</span>
                                <svg class="size-4 transition-transform in-aria-expanded:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                        </div>
                        <ul id="sub-layanan" x-show="open" x-cloak x-transition.opacity.duration.200ms class="mb-3 ml-5 border-l-2 border-white/15">
                            @foreach ($services as $service)
                                <li><a href="{{ route('services') }}#ruang-lingkup-{{ $service['slug'] }}" :aria-current="activeService === '{{ $service['slug'] }}' ? 'true' : false" class="aria-[current=true]:font-semibold aria-[current=true]:text-brand-bright block py-2.5 pl-5 text-sm text-white/60 transition-colors hover:text-white">{{ $service['name'] }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['href'] }}" @if ($current) aria-current="page" @endif
                           class="group flex items-center justify-between border-l-2 border-transparent py-4 pl-5 pr-5 font-display text-lg font-bold tracking-tight text-white/80 transition-colors hover:text-white aria-[current=page]:border-brand-bright aria-[current=page]:bg-white/[0.06] aria-[current=page]:text-white">
                            <span>{{ $item['label'] }}</span><x-ui.icon name="arrow-right" class="size-4 text-white/25 transition-all group-hover:translate-x-1 group-hover:text-brand-bright group-aria-[current=page]:text-brand-bright" />
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="shrink-0 border-t border-white/10 p-5">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="group mb-4 flex items-center justify-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/60 transition-colors hover:text-white">
                    <x-ui.icon name="dashboard" class="size-4" />
                    Kembali ke Dashboard Admin
                </a>
            @endauth
            <x-ui.button :href="route('contact')" class="w-full uppercase tracking-wider">
                Kontak Kami
                <x-ui.icon name="arrow-right" class="size-4" />
            </x-ui.button>
            <p class="mt-4 text-center text-[11px] font-semibold uppercase tracking-[0.25em] text-white/35">One Stop Solution</p>
        </div>
    </nav>
</header>
