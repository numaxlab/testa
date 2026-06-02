<div class="container mx-auto px-4">
    <x-numaxlab-atomic::organisms.tier class="mb-10">
        <x-numaxlab-atomic::organisms.tier.header>
            <h2 class="at-heading is-2">
                {{ $tier->name }}
            </h2>

            @if ($tier->has_link)
                <a href="{{ $tier->link }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ $tier->link_name }}
                </a>
            @endif
        </x-numaxlab-atomic::organisms.tier.header>

        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($articles as $article)
                <li>
                    <x-testa::articles.summary :article="$article"/>
                </li>
            @endforeach
        </ul>
    </x-numaxlab-atomic::organisms.tier>
</div>