<article>
    <div class="container mx-auto px-4">
        <header class="md:flex gap-6">
            <div class="md:w-1/2 lg:pr-20">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}" wire:navigate>
                            {{ __('Librería') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trafikrak.storefront.bookshop.itineraries.index') }}" wire:navigate>
                            {{ __('Itinerarios') }}
                        </a>
                    </li>
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1">{{ $itinerary->translateAttribute('name') }}</h1>

                @if ($itinerary->translateAttribute('subtitle'))
                    <h2 class="at-heading is-3 font-normal">{{ $itinerary->translateAttribute('subtitle') }}</h2>
                @endif

                @if ($itinerary->translateAttribute('description'))
                    <div class="mt-5">
                        {!! $itinerary->translateAttribute('description') !!}
                    </div>
                @endif
            </div>

            @if ($itinerary->hasMedia(config('lunar.media.collection')))
                <figure class="mt-5 md:w-1/2">
                    <img src="{{ $itinerary->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                         alt="">
                </figure>
            @endif
        </header>

        <x-numaxlab-atomic::organisms.tier class="mt-9">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Libros') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($itinerary->products as $product)
                    <li>
                        <x-trafikrak::products.summary
                                :product="$product"
                                :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>
</article>