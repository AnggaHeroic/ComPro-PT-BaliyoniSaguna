{{-- Props: about (konten: profile_image, profile = daftar paragraf, facts = data ringkas), company (memakai name). Profil lengkap perusahaan; susunan diadaptasi dari halaman Tentang Kami PT Putra Maju Sukses. --}}
@props(['about', 'company'])
<section id="profil" class="scroll-mt-20 bg-white">
    <div class="container-site">
        <div data-reveal-stagger class="grid gap-12 border-b border-black-soft/10 py-20 lg:grid-cols-12 lg:gap-20 lg:py-24">
            {{-- Deskripsi perusahaan (kiri): garis merah vertikal menaungi label dan judul. --}}
            <div class="lg:col-span-7">
                <div class="relative pl-7 sm:pl-8">
                    <span class="absolute inset-y-0 left-0 w-0.5 bg-brand" aria-hidden="true"></span>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">Profil Perusahaan</p>
                    <h2 class="mt-6 max-w-2xl text-3xl font-semibold leading-[1.08] tracking-[-0.025em] text-black-soft sm:text-4xl lg:text-[2.75rem]">
                        Dari <span class="text-brand">Teknologi Informasi</span> hingga Pengadaan Barang dan Jasa
                    </h2>
                </div>

                <div class="mt-8 max-w-2xl space-y-6 pl-7 text-justify text-sm leading-7 text-muted sm:pl-8 sm:text-base sm:leading-8">
                    @foreach ($about['profile'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>

            {{-- Foto dan data ringkas (kanan). Foto PLACEHOLDER (Picsum); ganti dengan foto resmi perusahaan. --}}
            <div class="lg:col-span-5">
                <div class="overflow-hidden bg-gray-tint">
                    <img src="https://images.unsplash.com/photo-{{ $about['profile_image'] }}?auto=format&fit=crop&w=800&h=600&q=75" alt="Kantor {{ $company['name'] }}" class="h-full min-h-[280px] w-full object-cover" loading="lazy" referrerpolicy="no-referrer">
                </div>
                <dl class="mt-6 divide-y divide-black-soft/10 border border-black-soft/15 bg-white px-7 sm:px-8">
                    @foreach ($about['facts'] as $fact)
                        <div class="py-5">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $fact['title'] }}</dt>
                            <dd class="mt-2 text-sm leading-6 text-black-soft">{{ $fact['text'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</section>
