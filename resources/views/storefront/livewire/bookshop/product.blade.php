<article class="container mx-auto px-4">
    <div class="lg:flex lg:flex-wrap lg:gap-10">
        <header class="lg:w-8/12">
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}">
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

            @if ($pricing)
                <div class="mt-5 mb-3 text-xl">
                    {{ $pricing->priceIncTax()->formatted() }}
                </div>
            @endif

            <livewire:numax-lab.lunar.geslib.storefront.livewire.components.add-to-cart
                    :key="'add-to-cart-' . $product->id"
                    :purchasable="$product->variant"/>

            <a class="at-button border-primary text-primary mt-3">
                Descarga este libro
            </a>

            <a class="at-button border-primary text-primary mt-3">
                Haz una donación
            </a>

            <a class="at-button border-primary text-primary mt-3">
                Descargar ficha
            </a>

            <a class="at-button border-primary text-primary mt-3">
                Descargar portada
            </a>
        </div>

        <div class="mt-10 lg:w-8/12 lg:ml-[25%] lg:pl-10">
            <div class="lg:hidden">
                @include('lunar-geslib::storefront.partials.product.body')
            </div>

            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Reseñas') }}
                    </h2>

                    <a class="at-small">
                        {{ __('Ver más') }}
                    </a>
                </x-numaxlab-atomic::organisms.tier.header>

                <ul class="flex flex-col gap-4 md:flex-row md:gap-6">
                    @for($i=0; $i<2; $i++)
                        <li class="pr-10">
                            <x-trafikrak::reviews.summary/>
                        </li>
                    @endfor
                </ul>
            </x-numaxlab-atomic::organisms.tier>

            @if ($product->associations->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Relacionados') }}
                        </h2>
                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4">
                        @foreach ($product->associations as $association)
                            <li>
                                <x-trafikrak::products.summary
                                        :product="$association->target"
                                        :href="route('trafikrak.storefront.bookshop.products.show', $association->target->defaultUrl->slug)"
                                />
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif

            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Audiovisual') }}
                    </h2>

                    <a class="at-small">
                        {{ __('Ver más') }}
                    </a>
                </x-numaxlab-atomic::organisms.tier.header>

                Dos audiovisuales relacionados con el libro, si los hubiera.
            </x-numaxlab-atomic::organisms.tier>

            @if ($itineraries->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Itinerarios') }}
                        </h2>
                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul>
                        @foreach($itineraries as $collection)
                            <li class="mb-6">
                                <x-numaxlab-atomic::molecules.banner
                                        :href="route('trafikrak.storefront.bookshop.itineraries.show', $collection->defaultUrl->slug)">
                                    <h2 class="at-heading is-3 mb-4">{{ $collection->translateAttribute('name') }}</h2>

                                    @if ($collection->translateAttribute('description'))
                                        <x-slot:content>
                                            {!! $collection->translateAttribute('description') !!}
                                        </x-slot:content>
                                    @endif
                                </x-numaxlab-atomic::molecules.banner>
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Cursos') }}
                        </h2>

                        <a class="at-small">
                            {{ __('Ver más') }}
                        </a>
                    </x-numaxlab-atomic::organisms.tier.header>

                    Un curso relacionado con el libro, si lo hubiera.
                </x-numaxlab-atomic::organisms.tier>

                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Actividades') }}
                        </h2>

                        <a class="at-small">
                            {{ __('Ver más') }}
                        </a>
                    </x-numaxlab-atomic::organisms.tier.header>

                    Una actividad relacionada con el libro, si lo hubiera.
                </x-numaxlab-atomic::organisms.tier>
            </div>

            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Noticias') }}
                    </h2>

                    <a class="at-small">
                        {{ __('Ver más') }}
                    </a>
                </x-numaxlab-atomic::organisms.tier.header>

                Dos noticias relacionadas con el libro, si las hubiera.
            </x-numaxlab-atomic::organisms.tier>
        </div>
    </div>
</article>
