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

        <div class="overflow-x-auto">
        <ul class="grid grid-flow-col auto-cols-[35%] md:auto-cols-[20%] xl:auto-cols-[14%] gap-6">
            <li>
                Libros
            </li>
        </ul>
        </div>
    </x-numaxlab-atomic::organisms.tier>
</div>