<div>
    <div class="bg-black p-4 absolute inset-0 flex items-center">
        <form wire:submit="search" class="w-full">
            <div class="container mx-auto px-4">
                <div class="relative">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model.live="query"
                            name="query"
                            placeholder="{{ __('Escribe lo que estás buscando') }}"
                            aria-label="{{ __('Buscar en toda la web') }}"
                            autocomplete="off"
                            id="globalSearchInput"
                    />
                    <button
                            type="button"
                            class="text-black absolute inset-y-0 right-3"
                            @click="searchExpanded = false"
                            aria-label="{{ __('Cerrar buscador') }}"
                    >
                        <i class="icon icon-close" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            @if (! empty($query))
                <div class="pt-4 pb-10 bg-black text-white absolute left-0 w-full z-10">
                    <div class="container mx-auto px-4">
                        <div class="flex gap-10">
                            @foreach ($contentTypes as $key => $contentType)
                                <button
                                        type="button"
                                        class="text-3xl {{ $key === $contentTypeFilter ? 'font-bold' : '' }}"
                                        wire:click="setContentTypeFilter('{{ $key }}')"
                                >
                                    {{ $contentType }}
                                </button>
                            @endforeach
                        </div>
                        @if ($results->isNotEmpty())

                            @if ($estimatedTotalHits > 0)
                                <p class="block mt-5">
                                    @if ($results->count() > 9)
                                        {{ __('Mostrando los :quantity mejores resultados de :estimatedTotalHits', ['quantity' => $results->count(), 'estimatedTotalHits' => $estimatedTotalHits]) }}
                                    @elseif ($results->count() === 1)
                                        {{ __('Encontramos 1 resultado') }}
                                    @else
                                        {{ __('Encontramos :quantity resultados', ['quantity' => $results->count()]) }}
                                    @endif

                                    @if ($contentTypeFilter !== 'all')
                                        <button class="font-bold" type="submit">
                                            {{ __('Ver más') }}
                                        </button>
                                    @endif
                                </p>
                            @endif

                            <ul class="divide-y border-y mt-6">
                                @foreach ($results as $result)
                                    <li wire:key="global-search-result-{{ $result->id }}">
                                        <a
                                                href="{{ $result->url }}"
                                                class="at-small text-white flex gap-2 items-center py-2"
                                        >
                                            <i
                                                    class="fa-solid {{ $result->icon }} w-8 text-3xl text-center"
                                                    aria-hidden="true"
                                            ></i>

                                            <div>
                                                {{ $result->title }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm mt-10">{{ __('No hay resultados para tu búsqueda') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>