<article>
    <div class="container mx-auto px-4">
        <header>
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}">
                        {{ __('Librería') }}
                    </a>
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1 mb-6">
                {{ __('Buscador de libros') }}
            </h1>

            <form wire:submit.prevent="search">
                <div class="relative">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model="q"
                            name="q"
                            id="sectionQuery"
                            aria-label="{{ __('Buscar en la librería') }}"
                            autocomplete="off"
                    />
                    <button type="submit" aria-label="{{ __('Buscar') }}"
                            class="text-primary absolute inset-y-0 right-3">
                        <i class="fa-solid fa-search" aria-hidden="true"></i>
                    </button>
                </div>

                <fieldset class="flex flex-col gap-3 md:flex-row mt-3">
                    <div class="md:w-4/12 relative">
                        <livewire:trafikrak.storefront.livewire.components.bookshop.taxonomy-select/>
                    </div>

                    <div class="md:w-3/12">
                        <x-numaxlab-atomic::atoms.forms.select
                                wire:model.live="languageId"
                                name="language"
                                id="language"
                                aria-label="{{ __('Filtrar por idioma') }}"
                        >
                            <option value="">Todos los idiomas</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->id }}">
                                    {{ $language->translateAttribute('name') }}
                                </option>
                            @endforeach
                        </x-numaxlab-atomic::atoms.forms.select>
                    </div>

                    <div class="md:w-2/12">
                        <x-numaxlab-atomic::atoms.forms.select
                                wire:model.live="priceRange"
                                name="priceRange"
                                id="priceRange"
                                aria-label="{{ __('Filtrar por precio') }}"
                        >
                            <option value="">Todos los precios</option>
                            @foreach($this->priceRanges as $value => $tag)
                                <option value="{{ $value }}">
                                    {{ $tag }}
                                </option>
                            @endforeach
                        </x-numaxlab-atomic::atoms.forms.select>
                    </div>

                    <div class="md:w-3/12">
                        <x-numaxlab-atomic::atoms.forms.select
                                wire:model.live="availabilityId"
                                name="availability"
                                id="availability"
                                aria-label="{{ __('Filtrar por disponibilidad') }}"
                        >
                            <option value="">Todos las disponibilidades</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ $status->translateAttribute('name') }}
                                </option>
                            @endforeach
                        </x-numaxlab-atomic::atoms.forms.select>
                    </div>
                </fieldset>
            </form>
        </header>

        @if ($results->isEmpty())
            <p class="mt-10">{{ __('No hay resultados para tu búsqueda.') }}</p>
        @else
            <ul class="mt-10 grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($results as $product)
                    <li wire:key="result-{{ $product->id }}">
                        <x-trafikrak::products.summary
                                :product="$product"
                                :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</article>
