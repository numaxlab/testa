<article>
    <div class="container mx-auto px-4">
        <header>
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('testa.storefront.bookshop.homepage') }}">
                        {{ __('Librería') }}
                    </a>
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">{{ __('Itinerarios') }}</h1>

            <p class="mt-4 md:max-w-[70%] lg:max-w-[50%]">
                {{ app(Testa\Settings\TextSettings::class)->getItinerariesIntro() }}
            </p>
        </header>

        @if ($itineraries->isNotEmpty())
            <ul class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($itineraries as $collection)
                    <li>
                        <x-testa::itineraries.summary :collection="$collection"/>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</article>
