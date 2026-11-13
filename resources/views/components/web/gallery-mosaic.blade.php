{{-- Props: photos (daftar ID foto Unsplash PLACEHOLDER, memakai 6 pertama). Mosaik foto hitam-putih untuk slot aside di <x-web.hero-aside>: tiga kolom, kolom tengah digeser turun agar berundak, dengan satu garis merah di bawah tiap kolom. Di layar kecil tampil lebih pendek. --}}
@props(['photos' => []])
<div class="grid grid-cols-3 gap-3 sm:gap-4" aria-hidden="true">
    @foreach (array_chunk(array_slice($photos, 0, 6), 2) as $column)
        <div @class(['flex flex-col gap-3 sm:gap-4', 'mt-8 sm:mt-12' => $loop->index === 1, 'mb-8 sm:mb-12' => $loop->index !== 1])>
            @foreach ($column as $photo)
                <div @class(['relative overflow-hidden border border-white/10 bg-neutral-900', 'aspect-[4/5]' => $loop->first xor $loop->parent->index === 1, 'aspect-[5/4]' => ! ($loop->first xor $loop->parent->index === 1)])>
                    <img src="https://images.unsplash.com/photo-{{ $photo }}?auto=format&fit=crop&w=500&h=650&q=70" alt="" loading="eager" referrerpolicy="no-referrer" class="size-full object-cover grayscale">
                    <span class="absolute inset-0 bg-black-deep/30"></span>
                </div>
            @endforeach
            <span class="mx-auto mt-1 block h-1 w-16 bg-brand-bright"></span>
        </div>
    @endforeach
</div>
