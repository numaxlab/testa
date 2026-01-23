<x-slot name="description">{{ Str::limit(strip_tags($slides->first()?->description), 160) }}</x-slot>

<article>
    <div class="container mx-auto px-4">
        <header class="md:flex gap-6">
            <div class="md:w-1/2 lg:w-2/3">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('testa.storefront.editorial.homepage') }}">
                            {{ __('Editorial') }}
                        </a>
                    </li>
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1">{{ $collection->translateAttribute('name') }}</h1>

                @if ($collection->translateAttribute('subtitle'))
                    <h2 class="at-heading is-3 font-normal">{{ $collection->translateAttribute('subtitle') }}</h2>
                @endif

                @if ($collection->translateAttribute('description'))
                    <div class="mt-5">
                        {!! $collection->translateAttribute('description') !!}
                    </div>
                @endif
            </div>
        </header>

        @if ($products->isNotEmpty())
            <x-numaxlab-atomic::organisms.tier class="mt-9">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Últimos números') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($products as $product)
                        <li>
                            <x-testa::products.summary
                                    :product="$product"
                                    :href="route('testa.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                            />
                        </li>
                    @endforeach
                </ul>

                {{ $products->links() }}
            </x-numaxlab-atomic::organisms.tier>
        @endif
    </div>
</article>