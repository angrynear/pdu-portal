@if ($paginator->hasPages())
<nav class="mt-4 d-flex justify-content-between align-items-center">

    <div class="text-muted small">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }}
        of {{ $paginator->total() }} results
    </div>

    <ul class="pagination mb-0">

        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link"
                   href="{{ $paginator->previousPageUrl() }}"
                   rel="prev">&laquo;</a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)

            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">
                            {{ $page }}
                        </a>
                    </li>
                @endforeach
            @endif

        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link"
                   href="{{ $paginator->nextPageUrl() }}"
                   rel="next">&raquo;</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">&raquo;</span>
            </li>
        @endif

    </ul>
</nav>
@endif
