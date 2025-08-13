<x-numaxlab-atomic::organisms.tier class="mb-10">
    <x-numaxlab-atomic::organisms.tier.header>
        <h2 class="at-heading is-2">
            {{ __('Temas') }}
        </h2>

        <a href="{{ route('trafikrak.storefront.education.topics.index') }}"
           wire:navigate
           class="at-small"
        >
            {{ __('Ver más') }}
        </a>
    </x-numaxlab-atomic::organisms.tier.header>

    <ul class="grid gap-4 md:grid-cols-3">
        @foreach ($topics as $topic)
            <li>
                <x-trafikrak::education-topics.summary :topic="$topic"/>
            </li>
        @endforeach
    </ul>
</x-numaxlab-atomic::organisms.tier>