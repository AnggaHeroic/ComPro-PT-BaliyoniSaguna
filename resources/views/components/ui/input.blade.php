{{-- Props: name (wajib), label, type (default text; type password otomatis punya tombol tampilkan/sembunyikan), icon (nama x-ui.icon di awal kolom). Atribut lain (value, required, autocomplete, dll.) diteruskan ke <input>. --}}
@props(['name', 'label' => null, 'type' => 'text', 'icon' => null])
@php
    $isPassword = $type === 'password';
    $inputClasses = [
        'block w-full rounded-sm border bg-gray-tint py-3 text-base text-black-soft placeholder:text-muted/60 transition-colors focus:outline-none focus-visible:outline-none sm:text-sm',
        $icon ? 'pl-11' : 'pl-4',
        $isPassword ? 'pr-12' : 'pr-4',
        $errors->has($name)
            ? 'border-brand focus:border-brand focus:ring-1 focus:ring-brand'
            : 'border-black-soft/20 hover:border-black-soft/40 focus:border-black-soft focus:bg-white focus:ring-1 focus:ring-black-soft',
    ];
@endphp
<div @if ($isPassword) x-data="{ shown: false }" @endif>
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-black-soft">{{ $label }}</label>
    @endif
    <div class="relative {{ $label ? 'mt-2' : '' }}">
        @if ($icon)
            <x-ui.icon :name="$icon" class="pointer-events-none absolute left-4 top-1/2 size-[1.125rem] -translate-y-1/2 text-muted" />
        @endif
        <input id="{{ $name }}" name="{{ $name }}"
               @if ($isPassword) :type="shown ? 'text' : 'password'" type="password" @else type="{{ $type }}" @endif
               @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
               {{ $attributes->class($inputClasses) }}>
        @if ($isPassword)
            <button type="button" @click="shown = !shown" :aria-label="shown ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="shown"
                    class="absolute inset-y-0 right-0 inline-flex w-12 items-center justify-center text-muted transition-colors hover:text-black-soft">
                <x-ui.icon name="eye" class="size-5" x-show="!shown" />
                <x-ui.icon name="eye-off" class="size-5" x-show="shown" x-cloak />
            </button>
        @endif
    </div>
    @error($name)
        <p id="{{ $name }}-error" class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
            <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
        </p>
    @enderror
</div>
