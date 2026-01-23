<x-slot name="description">{{ Str::limit(strip_tags($topic->translateAttribute('description')), 160) }}</x-slot>

<article>
    <div class="container mx-auto px-4">
        <header>
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('testa.storefront.bookshop.homepage') }}">
                        {{ __('Librería') }}
                    </a>
                </li>
                <li>
                    {{ __('Materias') }}
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">
                {{ $topic->translateAttribute('name') }}
            </h1>

            @if ($topic->translateAttribute('description'))
                <div class="mt-5 md:w-1/2 lg:pr-20">
                    {!! $topic->translateAttribute('description') !!}
                </div>
            @endif

            <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
                <div class="relative w-full">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model="q"
                            name="q"
                            id="sectionQuery"
                            placeholder="{{ __('Buscar en esta materia') }}"
                            aria-label="{{ __('Buscar en esta materia') }}"
                            autocomplete="off"
                    />
                    <button type="submit" aria-label="{{ __('Buscar') }}"
                            class="text-primary absolute inset-y-0 right-3">
                        <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </header>

        @if ($products->isNotEmpty())
            <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($products as $product)
                    <li wire:key="product-{{ $product->id }}">
                        <x-testa::products.summary
                                :product="$product"
                                :href="route('testa.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>

            {{ $products->links() }}
        @else
            <p>{{ __('No hay resultados.') }}</p>
        @endif
    </div>
</article>
