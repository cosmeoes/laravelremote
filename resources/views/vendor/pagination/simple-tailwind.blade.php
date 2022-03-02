@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span></span>
        @else
            <button wire:click="previousPage" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-500 border border-gray-300 leading-5 focus:outline-none focus:ring ring-red-500 active:bg-red-500transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </button>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-500 border border-gray-300 leading-5 focus:outline-none focus:ring ring-red-500 active:bg-red-500 transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </button>
        @else
            <span></span>
        @endif
    </nav>
@endif
