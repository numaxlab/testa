<article class="container mx-auto px-4">
    <div class="mt-6 lg:flex lg:gap-6">
        <figure class="mb-6 lg:w-4/12">
            <img src="{{ Storage::url($event->image) }}" alt="">
        </figure>

        <div class="lg:w-8/12">
            <header class="mb-6">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('trafikrak.storefront.news.homepage') }}" wire:navigate>
                            {{ __('Actualidad') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trafikrak.storefront.activities.index') }}" wire:navigate>
                            {{ __('Actividades') }}
                        </a>
                    </li>
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1 mt-4">{{ $event->name }}</h1>

                @if ($event->subtitle)
                    <h2 class="at-heading is-3 font-normal mt-2">{{ $event->subtitle }}</h2>
                @endif

                @if ($event->eventType)
                    <div class="mt-6">
                        <a
                                href="{{ route('trafikrak.storefront.activities.index', ['t' => $event->eventType->id]) }}"
                                wire:navigate
                                class="at-tag is-primary"
                        >
                            {{ $event->eventType->name }}
                        </a>
                    </div>
                @endif
            </header>

            <div class="lg:flex lg:flex-row-reverse lg:gap-6">
                <div class="lg:w-4/12">
                    <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
                        <li class="pr-2">
                            <i class="icon icon-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $event->starts_at->format('Y-m-d H:i:s') }}">{{ $event->starts_at->format('d/m/Y H:i') }}</time>
                        </li>
                    </ul>
                    <div class="flex gap-2 border-b border-black py-2">
                        <i class="icon icon-info text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                        <p class="at-small">
                            {{ __('trafikrak::coursemodule.form.delivery_method.options.'.$event->delivery_method->value) }}
                            @if ($event->venue)
                                <br>
                                {{ $event->venue->name }}
                            @endif

                            @if ($event->alert)
                                <span class="block mt-2">{{ $event->alert }}</span>
                            @endif
                        </p>
                    </div>

                    @if ($event->register_url)
                        <a href="{{ $event->register_url }}" class="at-button is-primary mt-4">
                            {{ __('Inscríbete') }}
                        </a>
                    @endif
                </div>
                @if ($event->description)
                    <div x-data="lineClamp" class="my-10 lg:w-8/12 lg:mt-0">
                        <div x-ref="description" :class="{ 'line-clamp-14': !showMore }">
                            {!! $event->description !!}
                        </div>

                        <button x-show="!showMore" @click.prevent="showMore = true" class="text-primary">
                            {{ __('Leer más') }}
                        </button>
                    </div>
                @endif
            </div>

            @if ($event->speakers->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="sr-only">
                            {{ __('Ponentes') }}
                        </h2>
                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 mb-10 md:grid-cols-2">
                        @foreach ($event->speakers as $speaker)
                            <li>
                                <x-trafikrak::authors.summary :author="$speaker"/>
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif

            <livewire:trafikrak.storefront.livewire.components.news.event-media
                    lazy
                    :event="$event"
            />

            <livewire:trafikrak.storefront.livewire.components.news.event-products
                    lazy
                    :event="$event"
            />
        </div>
    </div>
</article>