<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('dashboard') }}">
                    {{ __('Mi cuenta') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Mis favoritos') }}
        </h1>
    </header>

    <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-3">
        @foreach ($favouriteProducts as $product)
            <li>
                <x-trafikrak::products.horizontal
                        :product="$product"
                        :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                >
                    <x-slot name="actions">
                        <button wire:click="removeFromFavourites({{ $product->id }})">
                            Eliminar
                        </button>
                    </x-slot>
                </x-trafikrak::products.horizontal>
            </li>
        @endforeach
    </ul>

    {{ $favouriteProducts->links() }}
</article>