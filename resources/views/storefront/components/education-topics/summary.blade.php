<x-numaxlab-atomic::molecules.banner
        :href="route('trafikrak.storefront.education.topics.show', $topic->defaultUrl->slug)"
        :class="$topic->getFirstMedia(config('lunar.media.collection')) ? 'has-media' : ''"
        :image-src="$topic->getFirstMedia(config('lunar.media.collection'))?->getUrl('medium')"
>
    {{ $topic->name }}

    @if ($topic->description)
        <x-slot:content>
            <p>{{ \Illuminate\Support\Str::limit(strip_tags($topic->description), 120) }}</p>
        </x-slot:content>
    @endif
</x-numaxlab-atomic::molecules.banner>