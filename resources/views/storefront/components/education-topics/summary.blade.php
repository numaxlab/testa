<x-numaxlab-atomic::molecules.banner
        :href="route('trafikrak.storefront.education.topics.show', $topic->defaultUrl->slug)">
    <h2 class="at-heading is-3 mb-4">
        {{ $topic->name }}
    </h2>

    <x-slot:content>
        Breve texto descriptivo del tema.
    </x-slot:content>
</x-numaxlab-atomic::molecules.banner>