@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm text-gray-500 leading-5">
                {!! __('Showing') !!}
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-medium">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>

        <div class="flex items-center gap-1 shrink-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-md leading-5 cursor-default whitespace-nowrap">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="{{ __('pagination.previous') }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md leading-5 hover:bg-gray-50 hover:text-gray-700 transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="px-2 py-1 text-sm text-gray-400 whitespace-nowrap">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-primary-600 border border-primary-600 rounded-md leading-5 whitespace-nowrap">{{ $page }}</span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md leading-5 hover:bg-gray-50 hover:text-gray-700 transition cursor-pointer whitespace-nowrap">{{ $page }}</button>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="{{ __('pagination.next') }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md leading-5 hover:bg-gray-50 hover:text-gray-700 transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-md leading-5 cursor-default whitespace-nowrap">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
