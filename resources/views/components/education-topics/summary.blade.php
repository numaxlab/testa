<x-numaxlab-atomic::molecules.banner
        :href="route('testa.storefront.education.topics.show', $topic->defaultUrl->slug)"
        :class="$topic->getFirstMedia(config('lunar.media.collection')) ? 'has-media' : ''"
        :image-src="$topic->getFirstMedia(config('lunar.media.collection'))?->getUrl('medium')"
>
    {{ $topic->name }}

    @if ($topic->description)
        <x-slot:content>
            @if ($topic->description)
                <p>{{ \Illuminate\Support\Str::limit(strip_tags($topic->description), 250) }}</p>
            @endif
        </x-slot:content>
    @endif
</x-numaxlab-atomic::molecules.banner>