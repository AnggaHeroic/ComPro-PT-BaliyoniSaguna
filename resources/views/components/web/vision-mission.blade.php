{{-- Props: about (konten: direction_tagline, vision = kalimat visi, motto = dua baris moto, mission = daftar misi berisi title dan text). Disusun seperti bagian Visi & Misi PT Putra Maju Sukses: pita judul gelap berhias pita miring, lalu kartu visi dan kartu misi berdampingan dengan garis merah di atas tiap kartu. Kolom kiri berisi kartu visi dan kartu moto bertumpuk. Judul tiap kartu dipertegas ikon merah solid yang dipudarkan di pojok kanan atas kartu. --}}
@props(['about'])
<section id="visi-misi" class="scroll-mt-20 border-t border-black-soft/10 bg-gray-tint">
    {{-- Pita judul: judul tegas di tengah, diapit pita miring tipis di kiri dan kanan (senada dengan hero halaman ini); hanya di layar lebar. --}}
    <div class="relative isolate overflow-hidden border-y border-brand bg-black-deep">
        <span class="absolute inset-y-0 left-[10%] -z-10 hidden w-12 skew-x-[20deg] bg-white/[0.04] lg:block" aria-hidden="true"></span>
        <span class="absolute inset-y-0 left-[calc(10%+4.25rem)] -z-10 hidden w-1 skew-x-[20deg] bg-brand/60 lg:block" aria-hidden="true"></span>
        <span class="absolute inset-y-0 right-[10%] -z-10 hidden w-12 -skew-x-[20deg] bg-white/[0.04] lg:block" aria-hidden="true"></span>
        <span class="absolute inset-y-0 right-[calc(10%+4.25rem)] -z-10 hidden w-1 -skew-x-[20deg] bg-brand/60 lg:block" aria-hidden="true"></span>
        <div class="container-site py-12 text-center sm:py-14 lg:py-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand-bright">Arah Perusahaan</p>
            <h2 class="mt-3 font-display text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl">Visi dan Misi</h2>
            <span class="mx-auto mt-5 block h-0.5 w-12 bg-brand-bright" aria-hidden="true"></span>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-white/70 sm:text-lg">{{ $about['direction_tagline'] }}</p>
        </div>
    </div>

    <div class="container-site pb-20 pt-7 sm:pb-24 sm:pt-8 lg:pb-28 lg:pt-9">
        <div data-reveal-stagger class="grid gap-4 lg:grid-cols-2">
            {{-- Kolom kiri: kartu visi dan kartu moto berbagi tinggi sama rata, total tingginya menyamai kartu misi. --}}
            <div class="flex flex-col gap-4">
                <article class="relative flex flex-1 flex-col border border-black-soft/20 bg-white px-6 py-6 sm:px-8 sm:py-7 lg:py-8">
                    <span class="absolute inset-x-0 top-0 h-0.5 bg-brand" aria-hidden="true"></span>
                    <x-web.direction-icon name="vision" class="pointer-events-none absolute right-4 top-2 size-20 opacity-[0.15]" />
                    <div class="pt-3 text-center">
                        <p class="pl-[0.06em] text-lg font-black uppercase tracking-[0.06em] text-brand">Visi</p>
                        {{-- Garis merah tipis di bawah judul, selebar sepertiga area isi kartu. Memakai border (bukan kotak setinggi 1px) karena border selalu dibulatkan ke piksel penuh; kotak 1px pada posisi pecahan (kartu moto, tingginya dari flex) tampak pudar. --}}
                        <span class="mx-auto mt-4 block w-1/3 border-t border-brand" aria-hidden="true"></span>
                    </div>
                    <div class="mx-auto flex w-full max-w-xl flex-1 flex-col justify-center py-8">
                        <h3 class="font-display text-xl font-semibold leading-[1.3] tracking-[-0.025em] text-black-soft sm:text-2xl">{{ $about['vision'] }}</h3>
                        <span class="mt-5 block h-0.5 w-12 bg-brand" aria-hidden="true"></span>
                    </div>
                </article>

                <article class="relative flex flex-1 flex-col border border-black-soft/20 bg-white px-6 py-6 sm:px-8 sm:py-7 lg:py-8">
                    <span class="absolute inset-x-0 top-0 h-0.5 bg-brand" aria-hidden="true"></span>
                    <x-web.direction-icon name="motto" class="pointer-events-none absolute right-4 top-2 size-20 opacity-[0.15]" />
                    <div class="pt-3 text-center">
                        <p class="pl-[0.06em] text-lg font-black uppercase tracking-[0.06em] text-brand">Moto</p>
                        {{-- Garis merah tipis di bawah judul, selebar sepertiga area isi kartu. Memakai border (bukan kotak setinggi 1px) karena border selalu dibulatkan ke piksel penuh; kotak 1px pada posisi pecahan (kartu moto, tingginya dari flex) tampak pudar. --}}
                        <span class="mx-auto mt-4 block w-1/3 border-t border-brand" aria-hidden="true"></span>
                    </div>
                    <div class="mx-auto flex w-full max-w-xl flex-1 flex-col justify-center py-8">
                        <p class="text-center font-display text-xl font-semibold leading-snug tracking-[-0.02em] text-black-soft sm:text-2xl">
                            <span class="block">{{ $about['motto'][0] }}</span>
                            <span class="block text-brand">{{ $about['motto'][1] }}</span>
                        </p>
                        <span class="mx-auto mt-5 block h-0.5 w-12 bg-brand" aria-hidden="true"></span>
                    </div>
                </article>
            </div>

            {{-- Misi: jumlah butir mengikuti isi config. Butir yang disorot mendapat garis merah di kiri dan latar tipis. --}}
            <article data-reveal class="relative border border-black-soft/20 bg-white px-6 py-6 sm:px-8 sm:py-7 lg:py-8">
                <span class="absolute inset-x-0 top-0 h-0.5 bg-brand" aria-hidden="true"></span>
                    <x-web.direction-icon name="mission" class="pointer-events-none absolute right-4 top-2 size-20 opacity-[0.15]" />
                <div class="pt-3 text-center">
                        <p class="pl-[0.06em] text-lg font-black uppercase tracking-[0.06em] text-brand">Misi</p>
                        {{-- Garis merah tipis di bawah judul, selebar sepertiga area isi kartu. Memakai border (bukan kotak setinggi 1px) karena border selalu dibulatkan ke piksel penuh; kotak 1px pada posisi pecahan (kartu moto, tingginya dari flex) tampak pudar. --}}
                        <span class="mx-auto mt-4 block w-1/3 border-t border-brand" aria-hidden="true"></span>
                    </div>
                <ol class="mt-1">
                    @foreach ($about['mission'] as $item)
                        <li class="group relative -mx-3 flex items-start gap-4 border-t border-black-soft/10 px-3 py-3.5 first:border-t-0 transition-colors duration-200 hover:bg-gray-tint">
                            <span class="absolute inset-y-0 left-0 w-0.5 origin-top scale-y-0 bg-brand transition-transform duration-200 group-hover:scale-y-100" aria-hidden="true"></span>
                            <span class="w-7 shrink-0 font-display text-base font-black leading-6 text-brand transition-transform duration-200 group-hover:translate-x-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h4 class="font-display text-sm font-bold leading-6 text-black-soft transition-colors duration-200 group-hover:text-brand">{{ $item['title'] }}</h4>
                                <p class="text-sm leading-6 text-muted">{{ $item['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </article>
        </div>
    </div>
</section>
