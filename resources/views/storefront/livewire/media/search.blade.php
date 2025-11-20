<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.media.homepage') }}">
                    {{ __('Mediateca') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Audios y vídeos') }}
        </h1>

        @include('trafikrak::storefront.partials.media.search-form')
    </header>

    <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($media as $item)
            <li>
                <x-dynamic-component
                        :component="'trafikrak::'.$item->type.'.summary'"
                        :media="$item"/>
            </li>
        @endforeach
    </ul>
</article>