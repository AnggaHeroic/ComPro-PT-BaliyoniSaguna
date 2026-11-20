{{-- Props: name (default icon), selected (kunci ikon terpilih), label, options (kunci => label; default ServiceCategory::ICONS). Dropdown berisi kisi ikon; nama ikon tampil sebagai tooltip agar semua kotak seragam. --}}
@props(['name' => 'icon', 'selected' => null, 'label' => 'Ikon dari daftar', 'options' => \App\Models\ServiceCategory::ICONS])
<div x-data="iconPicker({ value: @js($selected), labels: @js($options) })"
     @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
    <span class="text-sm font-semibold text-black-soft">{{ $label }}</span>
    <input type="hidden" name="{{ $name }}" :value="value">

    {{-- Tombol: menampilkan ikon yang sedang dipilih. --}}
    <button type="button" @click="open = ! open" :aria-expanded="open" aria-haspopup="listbox"
            class="mt-2 flex w-full items-center gap-3 border border-black-soft/20 bg-gray-tint py-2 pl-2.5 pr-3 text-left text-sm text-black-soft transition-colors hover:border-black-soft/40 focus-visible:outline-2 focus-visible:outline-brand sm:w-72">
        <span class="flex size-10 shrink-0 items-center justify-center bg-black-soft text-white">
            @foreach ($options as $key => $text)
                <span x-show="value === '{{ $key }}'" @if ($selected !== $key) x-cloak @endif class="contents">
                    <x-web.service-icon :name="$key" accent="stroke-brand-bright" class="size-6" />
                </span>
            @endforeach
        </span>
        <span x-text="labels[value] ?? 'Pilih ikon'" class="min-w-0 flex-1 truncate font-semibold"></span>
        <x-ui.icon name="chevron-down" class="size-4 text-muted transition-transform" ::class="open && 'rotate-180'" />
    </button>

    {{-- Daftar ikon: hanya ikon, nama sebagai tooltip. --}}
    <div x-show="open" x-cloak x-transition.opacity.duration.100ms role="listbox" aria-label="Pilih ikon"
         class="absolute left-0 z-20 mt-2 grid w-[min(100%,22rem)] grid-cols-5 gap-1.5 border border-black-soft/15 bg-white p-2 shadow-[0_16px_40px_-12px_rgb(0_0_0/0.35)] sm:grid-cols-7 sm:w-[22rem]">
        @foreach ($options as $key => $text)
            <button type="button" role="option" title="{{ $text }}" aria-label="{{ $text }}" :aria-selected="value === '{{ $key }}'" @click="choose('{{ $key }}')"
                    :class="value === '{{ $key }}' ? 'bg-black-soft text-white' : 'text-black-soft hover:bg-gray-tint'"
                    class="flex aspect-square items-center justify-center transition-colors focus-visible:outline-2 focus-visible:outline-brand">
                <x-web.service-icon :name="$key" accent="stroke-brand-bright" class="size-6" />
            </button>
        @endforeach
    </div>

    @error($name)
        <p class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
            <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
        </p>
    @enderror
</div>
