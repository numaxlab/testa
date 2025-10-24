<article class="container mx-auto px-4">
    <header class="md:flex gap-6">
        <div class="md:w-1/2 lg:pr-20">
            <h1 class="at-heading is-1">{{ $author->name }}</h1>

            @if ($author->translateAttribute('biography'))
                <div class="mt-5">
                    {!! $author->translateAttribute('biography') !!}
                </div>
            @endif
        </div>

        @if ($author->hasMedia(config('lunar.media.collection')))
            <figure class="mt-5 md:w-1/2">
                <img src="{{ $author->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                     alt="">
            </figure>
        @endif
    </header>

    @if ($products->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mt-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Libros') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($products as $product)
                    <li>
                        <x-trafikrak::products.summary
                                :product="$product"
                                :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>

            {{ $products->links() }}
        </x-numaxlab-atomic::organisms.tier>
    @endif

    <div class="grid gap-6 md:grid-cols-3 mt-10">
        <x-numaxlab-atomic::organisms.tier>
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Audiovisual') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul>
                @for ($i=0; $i<2; $i++)
                    <li class="mb-6">
                        <x-trafikrak::audios.summary/>
                    </li>
                @endfor
            </ul>
        </x-numaxlab-atomic::organisms.tier>

        <x-numaxlab-atomic::organisms.tier>
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Itinerarios') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            Itinerarios...
        </x-numaxlab-atomic::organisms.tier>

        <div>
            <x-numaxlab-atomic::organisms.tier>
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Cursos') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                Cursos...
            </x-numaxlab-atomic::organisms.tier>

            <x-numaxlab-atomic::organisms.tier class="mt-6">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Actividades') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                Actividades...
            </x-numaxlab-atomic::organisms.tier>
        </div>
    </div>
</article>