<x-numaxlab-atomic::molecules.banner
        :class="$collection->getFirstMedia(config('lunar.media.collection')) ? 'has-media' : ''"
        :image-src="$collection->getFirstMedia(config('lunar.media.collection'))?->getUrl('medium')"
        :href="route('trafikrak.storefront.bookshop.itineraries.show', $collection->defaultUrl->slug)">
    {{ $collection->translateAttribute('name') }}

    @if ($collection->translateAttribute('description'))
        <x-slot:content>
            <p>{{ \Illuminate\Support\Str::limit(strip_tags($collection->translateAttribute('description')), 120) }}</p>
        </x-slot:content>
    @endif
</x-numaxlab-atomic::molecules.banner>