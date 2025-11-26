<article class="container mx-auto px-4">
    <div class="lg:flex lg:flex-wrap lg:gap-10">
        <header class="lg:w-8/12">
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                @if ($product->brand && $product->brand->translateAttribute('in-house') === true)
                    <li>
                        <a href="{{ route('trafikrak.storefront.editorial.homepage') }}" wire:navigate>
                            {{ __('Editorial') }}
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}" wire:navigate>
                            {{ __('Librería') }}
                        </a>
                    </li>

                    @if ($section)
                        <li>
                            <a
                                    @if ($section->defaultUrl)
                                        href="{{ route('trafikrak.storefront.bookshop.sections.show', ['slug' => $section->defaultUrl->slug]) }}"
                                    @endif
                                    wire:navigate
                            >
                                {{ $section->translateAttribute('name') }}
                            </a>
                        </li>
                    @endif
                @endif
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">
                {{ $product->recordTitle }}

                <button
                        class="text-primary"
                        aria-label="{{ __('Añadir a favoritos') }}"
                        wire:click="addToFavorites"
                        wire:key="fav-{{ $product->id }}"
                        wire:loading.attr="disabled"
                >
                    @if ($isUserFavourite)
                        <i class="icon icon-heart-full text-3xl" aria-hidden="true" wire:loading.remove></i>
                    @else
                        <i class="icon icon-heart text-3xl" aria-hidden="true" wire:loading.remove></i>
                    @endif

                    <div wire:loading>
                        @include('trafikrak::storefront.partials.spinner')
                    </div>
                </button>
            </h1>

            @if ($product->translateAttribute('subtitle'))
                <h2 class="at-heading is-3 mt-1">{{ $product->translateAttribute('subtitle') }}</h2>
            @endif

            @if ($product->authors->isNotEmpty())
                <p class="at-heading is-4 font-normal normal-case mt-3 text-primary">
                    @foreach ($product->authors as $author)
                        <a href="{{ route('trafikrak.storefront.authors.show', $author->defaultUrl->slug) }}">{{ $author->name }}</a>{{ $loop->last ? '' : '; ' }}
                    @endforeach
                </p>
            @endif

            <div class="hidden lg:block mt-8">
                @include('trafikrak::storefront.partials.product.body', ['prefix' => 'desktop'])
            </div>
        </header>

        <div class="bg-white lg:-order-1 lg:w-3/12 lg:sticky lg:top-10">
            <img
                    src="{{ $product->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                    alt="{{ __('Portada del libro :title', ['title' => $product->recordFullTitle]) }}"
                    class="w-full h-auto mt-2"
            >
        </div>

        <div class="mt-1 lg:w-8/12 lg:ml-[25%] lg:pl-10">
            <div class="lg:hidden mb-10">
                @include('trafikrak::storefront.partials.product.body', ['prefix' => 'mobile'])
            </div>

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-reviews
                    :key="$product->id . '-reviews'"
                    :product="$product"
                    lazy="true"
            />

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-associations
                    :key="$product->id . '-associations'"
                    :product="$product"
                    lazy="true"
            />

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-media
                    :product="$product"
                    lazy="true"
            />

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-itineraries
                    :key="$product->id . '-itineraries'"
                    :product="$product"
                    lazy="true"
            />

            <livewire:trafikrak.storefront.livewire.components.bookshop.product-activities
                    :product="$product"
                    lazy="true"
            />
        </div>
    </div>
</article>
