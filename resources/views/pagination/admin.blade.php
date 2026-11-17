{{-- Tampilan paginasi panel admin (dipasang sebagai tampilan bawaan di AppServiceProvider). Variabel dari Laravel: $paginator, $elements. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginasi" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">
            Menampilkan <span class="font-semibold text-black-soft">{{ $paginator->firstItem() }}</span>
            sampai <span class="font-semibold text-black-soft">{{ $paginator->lastItem() }}</span>
            dari <span class="font-semibold text-black-soft">{{ $paginator->total() }}</span>
        </p>

        <ul class="inline-flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <li><span aria-disabled="true" class="inline-flex size-9 cursor-not-allowed items-center justify-center border border-black-soft/10 text-black-soft/30"><x-ui.icon name="arrow-left" class="size-4" /></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="inline-flex size-9 items-center justify-center border border-black-soft/15 bg-white text-black-soft transition-colors hover:border-black-soft hover:bg-black-soft hover:text-white"><x-ui.icon name="arrow-left" class="size-4" /></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="inline-flex size-9 items-center justify-center text-sm text-muted">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span aria-current="page" class="inline-flex size-9 items-center justify-center border border-black-soft border-b-brand bg-black-soft text-sm font-bold text-white">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="inline-flex size-9 items-center justify-center border border-black-soft/15 bg-white text-sm font-semibold text-black-soft transition-colors hover:border-black-soft hover:bg-black-soft hover:text-white">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="inline-flex size-9 items-center justify-center border border-black-soft/15 bg-white text-black-soft transition-colors hover:border-black-soft hover:bg-black-soft hover:text-white"><x-ui.icon name="arrow-right" class="size-4" /></a></li>
            @else
                <li><span aria-disabled="true" class="inline-flex size-9 cursor-not-allowed items-center justify-center border border-black-soft/10 text-black-soft/30"><x-ui.icon name="arrow-right" class="size-4" /></span></li>
            @endif
        </ul>
    </nav>
@endif
