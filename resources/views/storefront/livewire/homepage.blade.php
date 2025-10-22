<div>
    <article class="bg-secondary pt-5 pb-90 px-5 mb-10">
        <div class="container mx-auto px-4">
            <h1>Destacado de portada</h1>
        </div>
    </article>

    @foreach ($tiers as $tier)
        <p>{{ $tier->type }}</p>
    @endforeach

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    Librería
                </h2>

                <a href="{{ route('trafikrak.storefront.bookshop.homepage') }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ __('Ver máis') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
                <li>
                    Libros
                </li>
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    Editorial
                </h2>

                <a href="{{ route('trafikrak.storefront.editorial.homepage') }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ __('Ver máis') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
                <li>
                    Libros
                </li>
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>

    <article class="bg-secondary pt-5 pb-50 px-5 mb-10">
        <div class="container mx-auto px-4">
            <h2>Báner 1</h2>
        </div>
    </article>

    <div class="container mx-auto px-4">
        <article class="bg-secondary pt-5 pb-50 px-5 mb-10">
            <h2>Báner 2</h2>
        </article>
    </div>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    Próximos cursos
                </h2>

                <a href="{{ route('trafikrak.storefront.education.homepage') }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ __('Ver máis') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            Cursos
        </x-numaxlab-atomic::organisms.tier>
    </div>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    Actividades
                </h2>

                <a href=""
                   wire:navigate
                   class="at-small"
                >
                    {{ __('Ver máis') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            Actividades
        </x-numaxlab-atomic::organisms.tier>
    </div>
</div>