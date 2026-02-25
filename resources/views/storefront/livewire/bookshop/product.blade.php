<x-slot name="description">{{ Str::limit(strip_tags($synopsis), 160) }}</x-slot>
<x-slot name="ogImage">{{ $product->getFirstMediaUrl(config('lunar.media.collection'), 'open-graph') }}</x-slot>

<x-slot name="head">
    <meta property="og:type" content="book">
    @if ($product->variant->gtin)
        <meta property="book:isbn" content="{{ $product->variant->gtin }}">
    @endif
    @if ($product->authors->isNotEmpty())
        @foreach ($product->authors as $author)
            <meta property="book:author" content="{{ $author->name }}">
        @endforeach
    @endif
    @if ($product->translateAttribute('issue-date'))
        <meta property="book:release_date" content="{{ $product->translateAttribute('issue-date') }}">
    @endif
    @if ($taxonomies->isNotEmpty())
        @foreach ($taxonomies as $taxonomy)
            <meta property="book:tag" content="{{ $taxonomy['name'] }}">
        @endforeach
    @endif
    <meta property="og:image:alt"
          content="{{ __('Portada del libro :title', ['title' => $product->recordFullTitle]) }}"/>
</x-slot>

<article class="container mx-auto px-4">
    <div class="lg:flex lg:flex-wrap lg:gap-10">
        <header class="lg:w-8/12">
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                @if ($isEditorialProduct)
                    <li>
                        <a href="{{ route('testa.storefront.editorial.homepage') }}" wire:navigate>
                            {{ __('Editorial') }}
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('testa.storefront.bookshop.homepage') }}" wire:navigate>
                            {{ __('Librería') }}
                        </a>
                    </li>

                    @if ($section)
                        <li>
                            <a
                                    @if ($section->defaultUrl)
                                        href="{{ route('testa.storefront.bookshop.sections.show', ['slug' => $section->defaultUrl->slug]) }}"
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
                        <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="sr-only">{{ __('Cargando...') }}</span>
                    </div>
                </button>
            </h1>

            @if ($product->translateAttribute('subtitle'))
                <h2 class="at-heading is-3 mt-1">{{ $product->translateAttribute('subtitle') }}</h2>
            @endif

            @if ($product->authors->isNotEmpty())
                <p class="at-heading is-4 font-normal normal-case mt-3 text-primary">
                    @foreach ($product->authors as $author)
                        <a href="{{ route('testa.storefront.authors.show', $author->defaultUrl->slug) }}">{{ $author->name }}</a>{{ $loop->last ? '' : '; ' }}
                    @endforeach
                </p>
            @endif

            <div class="hidden lg:block mt-8">
                @include('testa::storefront.partials.product.body', ['prefix' => 'desktop'])
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
                @include('testa::storefront.partials.product.body', ['prefix' => 'mobile'])
            </div>

            <livewire:testa.storefront.livewire.components.bookshop.product-reviews
                    :key="$product->id . '-reviews'"
                    :product="$product"
                    lazy="true"
            />

            <livewire:testa.storefront.livewire.components.bookshop.product-associations
                    :key="$product->id . '-associations'"
                    :product="$product"
                    :is-editorial-product="$isEditorialProduct"
                    lazy="true"
            />

            <livewire:testa.storefront.livewire.components.bookshop.product-media
                    :product="$product"
                    lazy="true"
            />

            <livewire:testa.storefront.livewire.components.bookshop.product-itineraries
                    :key="$product->id . '-itineraries'"
                    :product="$product"
                    lazy="true"
            />

            <livewire:testa.storefront.livewire.components.bookshop.product-activities
                    :product="$product"
                    lazy="true"
            />
        </div>
    </div>
</article>
