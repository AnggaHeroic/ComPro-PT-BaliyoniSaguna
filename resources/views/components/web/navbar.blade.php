{{-- Props: company (profil), navigation (daftar menu), services (kategori untuk mega-menu), transparent (navbar transparan di atas hero) --}}
@props(['company', 'navigation', 'services', 'transparent' => false])
{{-- Perilaku (scroll, mega-menu, menu mobile) ada di resources/js/navbar.js. Header menjadi putih setelah scroll atau saat menu terbuka. --}}
<header x-data="navbar" data-site-header
        :data-scrolled="String(solid)" data-scrolled="false"
        @scroll.window.passive="onScroll()"
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
    <div class="container-site flex h-[4.25rem] items-center justify-between gap-6 lg:h-20">
        <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="{{ $company['name'] }} — Beranda">
            <x-web.logo size="size-11 lg:size-12" />
            <x-web.wordmark :tone="$transparent ? 'auto' : 'dark'" />
        </a>

        <nav class="hidden items-center gap-9 lg:flex" aria-label="Navigasi utama">
            @foreach ($navigation as $item)
                @if ($item['mega'] ?? false)
                    <x-web.nav-link :transparent="$transparent" aria-controls="mega-layanan"
                                    class="inline-flex items-center gap-1.5 aria-expanded:text-black-soft aria-expanded:after:scale-x-100"
                                    x-ref="megaTrigger" ::aria-expanded="mega"
                                    @click="toggleMega()" @mouseenter="hoverMega()" @mouseleave="leaveMega()">
                        {{ $item['label'] }}
                        <svg class="size-3.5 transition-transform in-aria-expanded:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </x-web.nav-link>
                @else
                    <x-web.nav-link :href="$item['href']" :active="$item['match'] && request()->routeIs($item['match'])" :transparent="$transparent">
                        {{ $item['label'] }}
                    </x-web.nav-link>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            {{-- Merah di atas hero transparan, hitam setelah scroll; selain itu hitam. --}}
            <x-ui.button href="/#kontak" :variant="$transparent ? 'primary' : 'dark'"
                         :class="'hidden !py-2.5 uppercase tracking-wider lg:inline-flex'.($transparent ? ' group-data-[scrolled=true]:bg-black-soft group-data-[scrolled=true]:hover:bg-brand' : '')">Kontak Kami</x-ui.button>

            <button type="button" aria-controls="menu-mobile" @click="menu = ! menu" :aria-expanded="menu" aria-expanded="false"
                    @class([
                        'inline-flex size-11 items-center justify-center rounded-sm transition-colors lg:hidden',
                        'text-white hover:bg-white/10 group-data-[scrolled=true]:text-black-soft group-data-[scrolled=true]:hover:bg-gray-tint' => $transparent,
                        'text-black-soft hover:bg-gray-tint' => ! $transparent,
                    ])>
                <span class="sr-only">Buka menu navigasi</span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
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
                <a href="/#layanan" class="group mt-6 inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
                    Lihat semua layanan
                    <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                </a>
            </div>
            <ul class="grid auto-rows-min grid-flow-col grid-cols-4 gap-x-6 gap-y-1 [grid-template-rows:repeat(var(--rows),auto)]"
                style="--rows: {{ max(4, (int) ceil(count($services) / 4)) }}">
                @foreach ($services as $service)
                    <li>
                        <a href="/#layanan" class="flex items-center gap-3 border-l-2 border-transparent py-2.5 pl-3 text-[0.9rem] leading-snug text-black-soft transition-colors hover:border-brand hover:text-brand">
                            {{ $service['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Menu mobile (header otomatis putih saat menu terbuka) --}}
    <nav id="menu-mobile" x-show="menu" x-cloak @click="if ($event.target.closest('a')) menu = false" aria-label="Navigasi seluler"
         class="max-h-[calc(100dvh-4.25rem)] overflow-y-auto border-t border-black-soft/10 bg-white lg:hidden">
        <ul class="container-site py-2">
            @foreach ($navigation as $item)
                <li class="border-b border-black-soft/10" @if ($item['mega'] ?? false) x-data="{ open: false }" @endif>
                    @if ($item['mega'] ?? false)
                        <button type="button" aria-controls="sub-layanan" @click="open = ! open" :aria-expanded="open" aria-expanded="false"
                                class="flex w-full items-center justify-between py-4 text-sm font-semibold uppercase tracking-wider text-black-soft transition-colors hover:text-brand aria-expanded:text-brand">
                            <span>{{ $item['label'] }}</span>
                            <svg class="size-4 transition-transform in-aria-expanded:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <ul id="sub-layanan" x-show="open" x-cloak class="mb-3 ml-1 border-l-2 border-brand">
                            @foreach ($services as $service)
                                <li><a href="/#layanan" class="block py-2.5 pl-4 text-sm text-black-soft/80 transition-colors hover:text-brand">{{ $service['name'] }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['href'] }}" @if ($item['match'] && request()->routeIs($item['match'])) aria-current="page" @endif
                           class="flex items-center justify-between py-4 text-sm font-semibold uppercase tracking-wider text-black-soft transition-colors hover:text-brand aria-[current=page]:text-brand">
                            <span>{{ $item['label'] }}</span>
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </a>
                    @endif
                </li>
            @endforeach
            <li class="py-4">
                <x-ui.button href="/#kontak" variant="dark" class="w-full uppercase tracking-wider">Kontak Kami</x-ui.button>
            </li>
        </ul>
    </nav>
</header>
