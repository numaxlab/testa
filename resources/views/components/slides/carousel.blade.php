@props(['slides'])

@if ($slides->isNotEmpty())
    <div
            x-data="slidesCarousel({{ $slides->count() }})"
            @mouseenter="stop()"
            @mouseleave="start()"
            @keydown.window.arrow-left="prev()"
            @keydown.window.arrow-right="next()"
            class="relative overflow-hidden -mt-10 mb-10"
    >
        <div class="relative h-96 sm:h-[28rem]">
            @foreach ($slides as $i => $slide)
                <article
                        x-cloak
                        x-show="current === {{ $i }}"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 w-full h-full"
                >
                    @include("testa::components.slides._" . ($slide->variant?->value ?? 'contained'), ['slide' => $slide, 'i' => $i])
                </article>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <button
                    type="button"
                    class="absolute left-2 top-[50%] transform -translate-y-1/2 text-accent text-xl"
                    @click="prev()"
                    aria-label="{!! __('pagination.previous') !!}"
            >
                <i class="icon icon-arrow-left" aria-hidden="true"></i>
            </button>
            <button
                    type="button"
                    class="absolute right-2 top-[50%] transform -translate-y-1/2 text-accent text-xl"
                    @click="next()"
                    aria-label="{!! __('pagination.next') !!}"
            >
                <i class="icon icon-arrow-right" aria-hidden="true"></i>
            </button>
        @endif
    </div>
@endif
