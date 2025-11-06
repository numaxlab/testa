<div>
    @if ($itineraries->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
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
</div>