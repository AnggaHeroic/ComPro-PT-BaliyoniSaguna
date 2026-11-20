{{-- Props: name (wajib), label, hint (keterangan kecil di bawah kolom). Isi kolom = slot. Atribut lain (rows, required, maxlength, placeholder) diteruskan ke <textarea>. --}}
@props(['name', 'label' => null, 'hint' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-black-soft">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}"
              @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
              {{ $attributes->class([
                  'block w-full rounded-sm border bg-gray-tint px-4 py-3 text-base text-black-soft placeholder:text-muted/60 transition-colors focus:bg-white focus:outline-none focus-visible:outline-none sm:text-sm',
                  $label ? 'mt-2' : '',
                  $errors->has($name)
                      ? 'border-brand focus:border-brand focus:ring-1 focus:ring-brand'
                      : 'border-black-soft/20 hover:border-black-soft/40 focus:border-black-soft focus:ring-1 focus:ring-black-soft',
              ]) }}>{{ $slot }}</textarea>
    @if ($hint)
        <p class="mt-2 text-xs text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
            <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
        </p>
    @enderror
</div>
