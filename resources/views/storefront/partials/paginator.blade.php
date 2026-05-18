@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
        JS
        : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
            <div class="flex gap-2 items-center justify-between md:hidden">
                @if ($paginator->onFirstPage())
                    <span class="at-button is-secondary">
                    {!! __('pagination.previous') !!}
                </span>
                @else
                    <button
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                            class="at-button is-secondary"
                    >
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button
                            type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            class="at-button is-secondary"
                    >
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="at-button is-secondary">
                    {!! __('pagination.next') !!}
                </span>
                @endif
            </div>

            <div class="hidden md:block">
                <span class="inline-flex divide-x">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span
                                aria-disabled="true"
                                aria-label="{{ __('pagination.previous') }}"
                                class="px-2 cursor-not-allowed"
                        >
                            <i class="icon icon-arrow-left" aria-hidden="true"></i>
                        </span>
                    @else
                        <button
                                type="button"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                                class="inline-flex items-center px-2"
                                aria-label="{{ __('pagination.previous') }}"
                        >
                            <i class="icon icon-arrow-left" aria-hidden="true"></i>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-3 -ml-px text-sm font-medium cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-3 -ml-px text-sm font-medium cursor-default leading-5 text-accent">{{ $page }}</span>
                                    </span>
                                @else
                                    <button
                                            type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                            class="inline-flex items-center px-3 -ml-px text-sm font-medium leading-5"
                                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button
                                type="button"
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                                class="inline-flex items-center px-2"
                                aria-label="{{ __('pagination.next') }}"
                        >
                            <i class="icon icon-arrow-right" aria-hidden="true"></i>
                        </button>
                    @else
                        <span
                                aria-disabled="true"
                                aria-label="{{ __('pagination.next') }}"
                                class="px-2 cursor-not-allowed"
                        >
                            <i class="icon icon-arrow-right" aria-hidden="true"></i>
                        </span>
                    @endif
                </span>
            </div>
        </nav>
    @endif
</div>