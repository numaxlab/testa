<x-slot name="description">{{ Str::limit(strip_tags($section->translateAttribute('description')), 160) }}</x-slot>

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
                    {{ __('Secciones') }}
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">
                {{ $section->translateAttribute('name') }}
            </h1>

            @if ($section->translateAttribute('description'))
                <div class="mt-5 md:w-1/2 lg:pr-20">
                    {!! $section->translateAttribute('description') !!}
                </div>
            @endif

            <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
                <div class="relative md:w-1/2">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model="q"
                            name="q"
                            id="sectionQuery"
                            placeholder="{{ __('Buscar en esta sección') }}"
                            aria-label="{{ __('Buscar en esta sección') }}"
                            autocomplete="off"
                    />
                    <button type="submit" aria-label="{{ __('Buscar') }}"
                            class="text-primary absolute inset-y-0 right-3">
                        <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>

                @if ($section->children->isNotEmpty())
                    <div class="md:w-1/2">
                        <x-numaxlab-atomic::atoms.forms.select
                                wire:model="t"
                                wire:change="search"
                                name="t"
                                id="sectionTaxon"
                                aria-label="{{ __('Filtrar por taxonomía') }}"
                        >
                            <option value="">{{ __('Todas las taxonomías') }}</option>
                            @foreach($section->children as $child)
                                <option value="{{ $child->id }}" wire:key="taxon-{{ $child->id }}">
                                    {{ $child->translateAttribute('name') }}
                                </option>
                            @endforeach
                        </x-numaxlab-atomic::atoms.forms.select>
                    </div>
                @endif
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
