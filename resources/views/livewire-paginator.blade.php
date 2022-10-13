@if (count($paginator))
<div class="list-item-paginator uk-margin-medium-top">
    <div class="recap">
        <p class="uk-text-meta">
            da
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            di
            <span class="font-medium">{{ $paginator->total() }}</span>
            risultati
        </p>
    </div>

    @if($paginator->hasPages())
    <nav>
        <ul class="uk-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span aria-hidden="true" class="uk-hidden">&lsaquo;</span>
                </li>
            @else
                <li>
                    <a  wire:click.prevent="previousPage('{{ $paginator->getPageName() }}')" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"><i class="mvi mvi-chevron-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="uk-active" aria-current="page"><span  class="uk-badge">{{ $page }}</span></li>
                        @else
                            <li><a wire:click.prevent="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a wire:click.prevent="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"  href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"><i class="mvi mvi-chevron-right"></i></a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span aria-hidden="true" class="uk-hidden">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
    @endif
</div>
@endif
