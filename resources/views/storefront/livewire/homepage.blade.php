<div>
    @if ($slides->isNotEmpty())
        <div
                x-data="{
                    current: 0,
                    count: {{ $slides->count() }},
                    playing: true,
                    interval: null,
                    next() { this.current = (this.current + 1) % this.count },
                    prev() { this.current = (this.current - 1 + this.count) % this.count },
                    go(i) { this.current = i },
                    start() { this.playing = true; this.stop(); this.interval = setInterval(() => this.next(), 5000) },
                    stop() { if (this.interval) { clearInterval(this.interval); this.interval = null } this.playing = false },
                    init() { if (this.count > 1) this.start() }
                }"
                x-init="init()"
                @mouseenter="stop()"
                @mouseleave="start()"
                @keydown.window.arrow-left="prev()"
                @keydown.window.arrow-right="next()"
                class="relative overflow-hidden -mt-10 mb-10"
        >
            <div class="relative h-64 sm:h-96">
                @foreach ($slides as $i => $slide)
                    <article
                            @if ($i !== 0)
                                x-cloak
                            @endif
                            x-show="current === {{ $i }}"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0 w-full h-full flex items-center justify-center bg-black text-white"
                    >
                        @if ($slide->image)
                            <div class="w-full lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-4/7">
                                <img src="{{ Storage::url($slide->image) }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="relative container mx-auto px-4">
                            <div class="w-full p-8 lg:w-3/7 lg:py-8 lg:pr-20 lg:pl-0">
                                @if ($i === 0)
                                    <h1 class="at-heading is-2 mb-2">{{ $slide->name }}</h1>
                                @else
                                    <h2 class="at-heading is-2 mb-2">{{ $slide->name }}</h2>
                                @endif

                                @if ($slide->description)
                                    <div class="mb-4">
                                        {!! $slide->description !!}
                                    </div>
                                @endif

                                @if ($slide->button_text && $slide->link)
                                    <a href="{{ $slide->link }}" class="at-button is-primary">
                                        {{ $slide->button_text }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    @foreach ($tiers as $tier)
        <livewire:dynamic-component
                :component="'trafikrak.storefront.livewire.components.tier.' . $tier->livewire_component"
                :lazy="!$loop->first"
                :tier="$tier"
                :key="$tier->id"
        />
    @endforeach
</div>