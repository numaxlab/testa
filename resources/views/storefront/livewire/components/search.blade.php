<div>
    <div class="bg-primary p-4 absolute inset-0 flex items-center">
        <div class="w-full">
            <div class="container mx-auto px-4">
                <div class="relative">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model.live="query"
                            name="query"
                            placeholder="{{ __('Escribe lo que estás buscando') }}"
                            aria-label="{{ __('Buscar en toda la web') }}"
                            autocomplete="off"
                    />
                    <button
                            type="button"
                            class="text-primary absolute inset-y-0 right-3"
                            @click="searchExpanded = false"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('Cerrar buscador') }}</span>
                    </button>
                </div>
            </div>

            @if ($results->isNotEmpty())
                <div class="pt-4 pb-10 bg-primary text-white absolute left-0 w-full z-10">
                    <div class="container mx-auto px-4">
                        <div class="flex gap-10">
                            @foreach ($contentTypes as $key => $contentType)
                                <button
                                        class="text-3xl {{ $key === $contentTypeFilter ? 'font-bold' : '' }}"
                                        wire:click="setContentTypeFilter('{{ $key }}')"
                                >
                                    {{ $contentType }}
                                </button>
                            @endforeach
                        </div>
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

                        @if ($estimatedTotalHits > 0)
                            <small class="block mt-5">
                                {{ __('Mostrando los 10 mejores resultados de :estimatedTotalHits', ['estimatedTotalHits' => $estimatedTotalHits]) }}
                            </small>
                        @endif

                        @if ($contentTypeFilter !== 'all')
                            <button class="mt-8 text-3xl" wire:click="search">
                                {{ __('Ver más') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>