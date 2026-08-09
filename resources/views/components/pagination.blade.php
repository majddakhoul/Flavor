@if ($paginator->hasPages())
    <nav aria-label="{{ __('app.pagination') }}">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="is-disabled"><span><x-icon name="back" /></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="back" /></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="is-disabled"><span>{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="is-current" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next"><x-icon name="arrow" /></a></li>
            @else
                <li class="is-disabled"><span><x-icon name="arrow" /></span></li>
            @endif
        </ul>
    </nav>
@endif
