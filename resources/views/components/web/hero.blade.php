{{-- Props: tidak ada. Hero setinggi layar penuh, konten rata tengah; navbar transparan melayang di atasnya. --}}
<section class="relative isolate flex min-h-[max(100vh,100dvh)] items-center overflow-hidden bg-black-deep text-white">
    {{-- Foto PLACEHOLDER sementara (Picsum, bebas pakai). Ganti dengan foto resmi perusahaan bila tersedia. --}}
    <img src="https://picsum.photos/id/1076/1920/1080" alt="" class="absolute inset-0 -z-30 size-full object-cover" fetchpriority="high" referrerpolicy="no-referrer">
    {{-- Lapisan gelap merata agar teks di tengah terbaca, plus vignette yang menggelapkan tepi. --}}
    <div class="absolute inset-0 -z-20 bg-black-deep/70" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-20 bg-[radial-gradient(ellipse_at_center,transparent_30%,rgb(0_0_0/0.65)_100%)]" aria-hidden="true"></div>
    {{-- Gradient atas agar navbar transparan tetap terbaca di atas foto. --}}
    <div class="absolute inset-x-0 top-0 -z-20 h-44 bg-linear-to-b from-black-deep/80 to-transparent" aria-hidden="true"></div>
    {{-- Gradient bawah untuk transisi ke bagian berikutnya. --}}
    <div class="absolute inset-x-0 bottom-0 -z-20 h-40 bg-linear-to-t from-black-deep/70 to-transparent" aria-hidden="true"></div>

    {{-- Aksen: kotak tipis bertumpuk (sudut tajam, selaras dengan tombol). Dekoratif saja. --}}
    <svg class="absolute -left-56 top-1/2 -z-10 hidden size-[34rem] -translate-y-1/2 sm:block lg:-left-44 lg:size-[40rem]" viewBox="0 0 400 400" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
        <rect x="140" y="140" width="120" height="120" stroke-opacity=".07"/>
        <rect x="95" y="95" width="210" height="210" stroke-opacity=".06"/>
        <rect x="50" y="50" width="300" height="300" stroke="#ff0000" stroke-opacity=".22"/>
        <rect x="5" y="5" width="390" height="390" stroke-opacity=".04"/>
    </svg>
    <svg class="absolute -right-56 top-1/2 -z-10 hidden size-[34rem] -translate-y-1/2 sm:block lg:-right-44 lg:size-[40rem]" viewBox="0 0 400 400" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
        <rect x="140" y="140" width="120" height="120" stroke-opacity=".07"/>
        <rect x="95" y="95" width="210" height="210" stroke="#ff0000" stroke-opacity=".22"/>
        <rect x="50" y="50" width="300" height="300" stroke-opacity=".06"/>
        <rect x="5" y="5" width="390" height="390" stroke-opacity=".04"/>
    </svg>

    <div class="container-site py-28 text-center sm:py-32">
        <div class="mx-auto flex max-w-4xl flex-col items-center">
            <div class="flex items-center gap-3 sm:gap-4">
                <span class="h-0.5 w-8 bg-brand-bright sm:w-12" aria-hidden="true"></span>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-white sm:text-sm">PT. Bal<span class="text-brand-bright">i</span>yoni Saguna</p>
                <span class="h-0.5 w-8 bg-brand-bright sm:w-12" aria-hidden="true"></span>
            </div>
            <h1 class="mt-6 font-black leading-[1.05] tracking-tight">
                <span class="block text-4xl sm:text-5xl lg:text-6xl">One Stop Solution</span>
                <span class="mt-3 block text-xl text-brand-bright sm:text-2xl lg:text-3xl">Solusi Tuntas, Bahagia Pantas</span>
            </h1>
            <p class="mt-8 max-w-2xl text-lg leading-relaxed text-white/80">
                Perusahaan induk dari berbagai holding dan outlet Baliyoni Group. Kami menyediakan peralatan elektronik, perkantoran, pendidikan, pendingin udara, hingga sistem parkir elektronik.
            </p>
            <div class="mt-10 flex w-full flex-col justify-center gap-3 sm:w-auto sm:flex-row">
                <x-ui.button href="/#layanan">
                    Lihat Layanan
                    <x-ui.icon name="arrow-right" class="size-4" />
                </x-ui.button>
                <x-ui.button href="/#kontak" variant="outline-light">Hubungi Kami</x-ui.button>
            </div>
        </div>
    </div>
</section>
