@extends('layouts.app', ['title' => 'Isi Halaman '.$definition['label']])

{{-- Variabel: $page (slug), $record (model Page), $definition (config/page_contents.php), $content (PageContentSet), $sections (kunci bagian), $initialSection, $lastChange. --}}
@section('content')
    <div class="space-y-6">
        <x-admin.page-header eyebrow="Isi Halaman" :title="$definition['label']" :description="$definition['description']" />

        <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              x-data="pageEditor({ active: @js($initialSection), sections: @js($sections) })" @invalid.capture="onInvalid($event)">
            @csrf
            @method('PUT')
            {{-- Bagian yang sedang dibuka, agar setelah Simpan halaman kembali ke bagian yang sama. --}}
            <input type="hidden" name="bagian" :value="active">

            <div class="max-w-3xl" x-ref="top">
                {{-- Pilih bagian --}}
                <div class="border border-black-soft/10 bg-white p-4 sm:p-5">
                    <h2 class="text-sm font-bold text-black-soft">Bagian halaman</h2>
                    <p class="mt-0.5 text-xs leading-5 text-muted">Pilih bagian yang ingin diubah. Perubahan di semua bagian disimpan sekaligus saat Anda menekan Simpan.</p>

                    <div class="mt-4 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap" role="tablist" aria-label="Bagian halaman">
                        @foreach ($definition['sections'] as $sectionKey => $section)
                            <button type="button" role="tab" id="tab-{{ $sectionKey }}" @click="select('{{ $sectionKey }}')" :aria-selected="active === '{{ $sectionKey }}'"
                                    :class="active === '{{ $sectionKey }}' ? 'border-black-soft bg-black-soft text-white' : 'border-black-soft/20 bg-white text-black-soft hover:border-black-soft/50 hover:bg-gray-tint'"
                                    class="flex min-h-11 items-center gap-2 border px-3 py-2 text-left text-sm font-semibold leading-tight transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-brand max-sm:[&:last-child:nth-child(odd)]:col-span-2 sm:min-h-0 sm:py-1.5">
                                <span :class="active === '{{ $sectionKey }}' ? 'bg-white/20 text-white' : 'bg-gray-tint text-muted'" class="flex size-5 shrink-0 items-center justify-center text-[11px] font-bold" aria-hidden="true">{{ $loop->iteration }}</span>
                                {{ $section['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Form per bagian: hanya bagian aktif yang tampil (semua tetap ikut terkirim saat Simpan). --}}
                <div class="mt-4">
                    @foreach ($definition['sections'] as $sectionKey => $section)
                        <div x-show="active === '{{ $sectionKey }}'" x-cloak data-section="{{ $sectionKey }}" role="tabpanel" aria-labelledby="tab-{{ $sectionKey }}">
                            <div class="border border-black-soft/10 bg-white p-5 sm:p-6">
                                {{-- Judul panel boleh berbeda dari label tab (mis. tab "CTA", judul "Ajakan menghubungi"). --}}
                                <h2 class="text-base font-black tracking-tight text-black-soft">{{ $section['title'] ?? $section['label'] }}</h2>
                                @isset($section['description'])
                                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ $section['description'] }}</p>
                                @endisset

                                @unless (isset($section['groups']))
                                    <div class="mt-5">
                                        @if ($section['preview'] ?? true)
                                            @php($target = is_string($section['preview'] ?? null) ? $section['preview'] : $sectionKey)
                                            <x-admin.section-preview :url="route('admin.pages.preview', ['page' => $page, 'section' => $target])" :section="$target" :tab="$sectionKey" />
                                        @else
                                            <p class="mb-6 bg-gray-tint px-3 py-2 text-xs text-muted">{{ $section['note'] ?? 'Bagian ini tidak memiliki pratinjau.' }}</p>
                                        @endif

                                        <div class="space-y-6">
                                            @foreach ($section['fields'] as $fieldKey => $field)
                                                <div><x-admin.page-field :field-key="$fieldKey" :field="$field" :content="$content" /></div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endunless
                            </div>

                            {{-- Bagian dengan beberapa variasi: tiap variasi adalah kartu terpisah dengan pratinjau dan kolomnya sendiri. --}}
                            @isset($section['groups'])
                                @foreach ($section['groups'] as $groupKey => $group)
                                    <div class="mt-4 border border-black-soft/10 bg-white p-5 sm:p-6">
                                        <h3 class="text-base font-black tracking-tight text-black-soft">{{ $group['label'] }}</h3>
                                        <p class="mb-5 mt-1 text-sm leading-relaxed text-muted">{{ $group['description'] }}</p>

                                        <x-admin.section-preview :url="route('admin.pages.preview', ['page' => $page, 'section' => $group['preview']])" :section="$group['preview']" :tab="$sectionKey" />

                                        <div class="space-y-6">
                                            @foreach ($section['fields'] as $fieldKey => $field)
                                                @if (($field['group'] ?? null) === $groupKey)
                                                    <div><x-admin.page-field :field-key="$fieldKey" :field="$field" :content="$content" /></div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endisset

                            {{-- Pindah antar bagian --}}
                            <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                                @if (! $loop->first)
                                    <button type="button" @click="select('{{ $sections[$loop->index - 1] }}')" class="inline-flex items-center gap-1 font-semibold text-muted transition-colors hover:text-brand">
                                        <x-ui.icon name="arrow-left" class="size-4" />
                                        Sebelumnya
                                    </button>
                                @else
                                    <span></span>
                                @endif
                                @if (! $loop->last)
                                    <button type="button" @click="select('{{ $sections[$loop->index + 1] }}')" class="inline-flex items-center gap-1 font-semibold text-muted transition-colors hover:text-brand">
                                        Berikutnya
                                        <x-ui.icon name="arrow-right" class="size-4" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Bilah simpan: ikut menggulir lalu menempel di dasar layar selama form belum habis. --}}
            <div class="sticky bottom-0 z-10 -mx-5 mt-8 border-t border-black-soft/10 bg-white/95 px-5 py-5 backdrop-blur sm:-mx-8 sm:px-8">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3">
                    <x-ui.button type="submit" class="uppercase tracking-wider">Simpan</x-ui.button>
                    <x-ui.button :href="route('admin.dashboard')" variant="outline" class="uppercase tracking-wider" data-unsaved-skip>Batal</x-ui.button>
                    <p class="text-xs text-muted max-sm:hidden">Perubahan baru tampil di website setelah Anda menekan Simpan.</p>
                </div>
            </div>
        </form>

        {{-- Setelan default halaman --}}
        <section class="max-w-3xl border border-black-soft/15 bg-white">
            <header class="border-b border-black-soft/10 px-5 py-4 sm:px-6">
                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Setelan default</h2>
                <p class="mt-1 text-xs leading-5 text-muted">
                    @if ($content->hasCustomDefaults())
                        Setelan default halaman ini ditetapkan admin pada {{ $record->defaults_set_at?->timezone(config('app.timezone'))->locale('id')->translatedFormat('j F Y, H:i') }}.
                    @else
                        Halaman ini memakai setelan default bawaan sistem.
                    @endif
                    "Setel default" menjadikan isi tersimpan saat ini sebagai acuan baru, dan "Reset default" mengembalikan seluruh isi halaman ke acuan tersebut.
                </p>
            </header>

            <div class="flex flex-wrap items-center gap-3 p-5 sm:px-6">
                <form method="POST" action="{{ route('admin.pages.default.set', $page) }}"
                      data-confirm-title="Setel default?" data-confirm-tone="default" data-confirm-label="Lanjutkan"
                      data-confirm-message="Isi halaman {{ $definition['label'] }} yang sudah tersimpan akan dijadikan setelan default baru. Setelan default yang lama diganti dan perubahan di form yang belum disimpan tidak ikut. Tampilan website tidak berubah."
                      data-confirm-second-title="Yakin menyetel default baru?" data-confirm-second-label="Ya, setel default" data-confirm-second-tone="default"
                      data-confirm-second-message="Setelan default baru mencakup seluruh isi halaman {{ $definition['label'] }} (teks, judul, daftar, dan foto) dan menggantikan setelan default sebelumnya.">
                    @csrf
                    <x-ui.button type="submit" variant="outline" class="!py-2.5">Setel default</x-ui.button>
                </form>

                <form method="POST" action="{{ route('admin.pages.reset', $page) }}"
                      data-confirm-title="Reset default?" data-confirm-label="Lanjutkan"
                      data-confirm-message="Seluruh isi halaman {{ $definition['label'] }} akan dikembalikan ke setelan default ({{ $content->hasCustomDefaults() ? 'yang Anda tetapkan lewat "Setel default"' : 'bawaan sistem' }}). Semua perubahan yang sudah tersimpan di halaman ini akan hilang."
                      data-confirm-second-title="Yakin mereset seluruh halaman?" data-confirm-second-label="Ya, reset default"
                      data-confirm-second-message="Seluruh teks, judul, daftar, dan foto halaman {{ $definition['label'] }} di website akan langsung berubah ke setelan default. Tindakan ini tidak dapat dibatalkan.">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="outline" class="!border-brand/30 !py-2.5 !text-brand hover:!bg-brand hover:!text-white">Reset default</x-ui.button>
                </form>

                @if ($content->hasCustomDefaults())
                    <form method="POST" action="{{ route('admin.pages.default.clear', $page) }}"
                          data-confirm-title="Kembali ke default semula?" data-confirm-tone="default" data-confirm-label="Lanjutkan"
                          data-confirm-message="Setelan default yang ditetapkan admin untuk halaman {{ $definition['label'] }} dihapus dan acuannya kembali ke teks bawaan sistem. Tampilan website saat ini tidak berubah."
                          data-confirm-second-title="Yakin menghapus default kustom?" data-confirm-second-label="Ya, hapus default kustom" data-confirm-second-tone="default"
                          data-confirm-second-message="Setelah ini, &quot;Reset default&quot; memakai teks dan foto bawaan sistem, bukan lagi default yang Anda tetapkan.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-1 py-2.5 text-sm font-semibold text-muted underline-offset-2 transition-colors hover:text-brand hover:underline">Pakai default bawaan sistem</button>
                    </form>
                @endif
            </div>
        </section>

        <x-admin.last-changed :log="$lastChange" />
    </div>
@endsection
