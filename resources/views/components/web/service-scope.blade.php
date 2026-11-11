{{-- Props: services (daftar kategori layanan; memakai name dan icon). Ringkasan ruang lingkup produk dan layanan. --}}
@props(['services'])
<section id="ruang-lingkup" class="scroll-mt-20 bg-black-deep py-20 text-white lg:py-28">
    <div class="container-site">
        <x-web.section-title eyebrow="Ruang Lingkup" title="Produk dan Layanan Kami" invert center>
            Beragam kategori yang dapat dipenuhi dalam satu tempat.
        </x-web.section-title>

        <ul class="mt-14 grid gap-px border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($services as $service)
                <li class="flex items-center gap-4 bg-black-deep p-5 transition-colors hover:bg-black-soft">
                    <x-web.service-icon :name="$service['icon']" accent="stroke-brand-bright" class="size-9 shrink-0 text-white" />
                    <span class="text-sm font-semibold leading-snug">{{ $service['name'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
