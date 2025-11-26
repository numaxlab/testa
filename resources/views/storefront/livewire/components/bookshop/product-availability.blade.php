<div class="font-serif font-small text-primary mb-3">
    {{ $status }}

    @if ($moreInfo)
        <div
                class="inline-block relative"
                x-data="{ open: false }"
                @keydown.escape="open = false"
                @click.away="open = false"
        >
            <button
                    type="button"
                    aria-label="{{ __('Más información') }}"
                    @click.stop="open = !open"
                    :aria-expanded="open ? 'true' : 'false'"
                    class="ml-2 focus:outline-none"
            >
                <i class="icon icon-info" aria-hidden="true"></i>
            </button>

            <div
                    x-cloak
                    x-show="open"
                    x-transition.opacity.duration.150
                    class="absolute left-1/2 bottom-full mb-2 w-64 transform -translate-x-1/2 bg-white text-sm text-gray-800 border border-gray-200 rounded shadow-lg p-3 z-50"
                    role="tooltip"
            >
                {!! $moreInfo !!}
            </div>
        </div>
    @endif
</div>