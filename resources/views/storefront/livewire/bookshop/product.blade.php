<article class="container mx-auto px-4">
    <div class="lg:flex lg:flex-wrap lg:gap-10">
        <header class="lg:w-8/12">
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}" wire:navigate>
                        {{ __('Librería') }}
                    </a>
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">
                {{ $product->recordTitle }}

                <button
                        class="text-primary"
                        aria-label="{{ __('Añadir a favoritos') }}"
                        wire:click="addToFavorites"
                        wire:loading.attr="disabled"
                >
                    @if ($isUserFavourite)
                        <i class="fa-solid fa-heart" aria-hidden="true" wire:loading.remove></i>
                    @else
                        <i class="fa-regular fa-heart" aria-hidden="true" wire:loading.remove></i>
                    @endif

                    <div wire:loading>
                        @include('trafikrak::storefront.partials.spinner')
                    </div>
                </button>
            </h1>

            @if ($product->translateAttribute('subtitle'))
                <h2 class="at-heading is-3">{{ $product->translateAttribute('subtitle') }}</h2>
            @endif

            @if ($product->authors->isNotEmpty())
                <p class="at-heading is-3 font-normal mt-3">
                    @foreach ($product->authors as $author)
                        <a href="{{ route('trafikrak.storefront.authors.show', $author->defaultUrl->slug) }}">{{ $author->name }}</a>{{ $loop->last ? '' : '; ' }}
                    @endforeach
                </p>
            @endif

            <div class="hidden lg:block mt-10">
                @include('trafikrak::storefront.partials.product.body')
            </div>
        </header>

        <div class="bg-white lg:-order-1 lg:w-3/12 lg:sticky lg:top-10">
            <img
                    src="{{ $product->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                    alt="{{ __('Portada del libro :title', ['title' => $product->recordFullTitle]) }}"
                    class="w-full h-auto mt-7"
            >
        </div>

        <div class="mt-10 lg:w-8/12 lg:ml-[25%] lg:pl-10">
            <div class="lg:hidden">
                @include('trafikrak::storefront.partials.product.body')
            </div>

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-reviews
                    :product="$product"
                    lazy="true"
            />

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-associations
                    :product="$product"
                    lazy="true"
            />

            {{--<livewire:trafikrak.storefront.livewire.components.bookshop.product-media
                    :product="$product"
                    lazy="true"
            />--}}

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-itineraries
                    :product="$product"
                    lazy="true"
            />

            {{--<livewire:trafikrak.storefront.livewire.components.bookshop.product-events
                    :product="$product"
                    lazy="true"
            />--}}
        </div>
    </div>
</article>
