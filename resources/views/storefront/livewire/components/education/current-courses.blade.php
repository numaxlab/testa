<x-numaxlab-atomic::organisms.tier class="mb-10">
    <x-numaxlab-atomic::organisms.tier.header>
        <h2 class="at-heading is-2">
            {{ __('Cursos') }}
        </h2>

        <a href="{{ route('trafikrak.storefront.education.courses.index') }}"
           wire:navigate
           class="at-small"
        >
            {{ __('Ver más') }}
        </a>
    </x-numaxlab-atomic::organisms.tier.header>

    @if ($courses->isEmpty())
        <p class="text-center">
            {{ __('No hay cursos disponibles en este momento.') }}
        </p>
    @else
        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-6">
            @foreach($courses as $course)
                <li class="{{ $loop->index > 1 ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    <x-trafikrak::courses.summary :course="$course"/>
                </li>
            @endforeach
        </ul>
    @endif
</x-numaxlab-atomic::organisms.tier>