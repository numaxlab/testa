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

            <h1 class="at-heading is-1">{{ __('Itinerarios') }}</h1>

            <p class="mt-4 md:max-w-[70%] lg:max-w-[50%]">
                Los itinerarios de formación y lectura es un proyecto donde tratamos de recoger los saberes acumulados a lo largo de los años por activistas y teóricos. Con estos caminos tratamos de recoger los mapas de nuestro quehacer político, y recuperar la memoria de esos caminos, que inevitablemente nos encontramos muchas veces de nuevo.
            </p>
        </header>

        @if ($itineraries->isNotEmpty())
            <ul class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($itineraries as $collection)
                    <li>
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
        @endif
    </div>
</article>
