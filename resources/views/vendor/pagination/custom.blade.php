@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-3 px-2">
        <!-- Tampilan Ringkas Mobile (HP) -->
        <div class="flex justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed min-h-[44px]">
                    &laquo; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-secondary px-4 py-2 text-xs">
                    &laquo; Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-primary px-4 py-2 text-xs">
                    Selanjutnya &raquo;
                </a>
            @else
                <span class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed min-h-[44px]">
                    Selanjutnya &raquo;
                </span>
            @endif
        </div>

        <!-- Tampilan Lengkap Desktop & Tablet -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-gray-600">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-bold text-gray-900 tabular-money">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-bold text-gray-900 tabular-money">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-bold text-gray-900 tabular-money">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-300 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-emas-50 hover:text-emas-800 hover:border-emas-300 transition" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-400">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-[#FAF4E4CC] bg-[#CEA042] rounded-xl shadow-sm border border-[#BA8832] tabular-money">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-emas-50 hover:text-emas-800 hover:border-emas-300 transition tabular-money" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-emas-50 hover:text-emas-800 hover:border-emas-300 transition" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-xs font-semibold text-gray-300 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
