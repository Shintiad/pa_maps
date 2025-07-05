@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&laquo;</a>
                </li>
            @endif

            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $delta = 1; // Number of pages to show on each side of the current page
                $leftPad = 1; // Number of pages to always show at the start
                $rightPad = 1; // Number of pages to always show at the end
            @endphp

            {{-- First Pages --}}
            @foreach (range(1, min($leftPad, $lastPage)) as $page)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Middle Pages --}}
            @if ($currentPage > $leftPad + $delta + 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif

            @foreach (range(max($leftPad + 1, $currentPage - $delta), min($lastPage - $rightPad, $currentPage + $delta)) as $page)
                @if ($page > $leftPad && $page < $lastPage - $rightPad + 1)
                    <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            @if ($currentPage < $lastPage - $rightPad - $delta)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif

            {{-- Last Pages --}}
            @foreach (range(max($lastPage - $rightPad + 1, $leftPad + 1), $lastPage) as $page)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif