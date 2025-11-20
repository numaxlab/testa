<article>
    <div class="container mx-auto px-4">
        <header>
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route('trafikrak.storefront.education.homepage') }}">
                        {{ __('Formación') }}
                    </a>
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>

            <h1 class="at-heading is-1">
                {{ __('Cursos') }}
            </h1>

            <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
                <div class="relative md:w-1/3">
                    <x-numaxlab-atomic::atoms.forms.input
                            type="search"
                            wire:model="q"
                            name="q"
                            id="query"
                            placeholder="{{ __('Buscar en la mediateca') }}"
                            aria-label="{{ __('Buscar en la mediateca') }}"
                            autocomplete="off"
                    />
                    <button
                            type="submit"
                            aria-label="{{ __('Buscar') }}"
                            class="text-primary absolute inset-y-0 right-3"
                    >
                        <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="md:w-1/3">
                    <x-numaxlab-atomic::atoms.forms.select
                            wire:model="t"
                            wire:change="search"
                            name="t"
                            id="topic"
                            aria-label="{{ __('Filtrar por tema') }}"
                    >
                        <option value="">{{ __('Todos los temas') }}</option>
                        @foreach ($topics as $topic)
                            <option value="{{ $topic->id }}" wire:key="topic-{{ $topic->id }}">
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </x-numaxlab-atomic::atoms.forms.select>
                </div>

                <div class="md:w-1/3">
                    <x-numaxlab-atomic::atoms.forms.select
                            wire:model="ty"
                            wire:change="search"
                            name="ty"
                            id="type"
                            aria-label="{{ __('Filtrar por tipología') }}"
                    >
                        <option value="">{{ __('Todos las tipologías') }}</option>
                    </x-numaxlab-atomic::atoms.forms.select>
                </div>
            </form>
        </header>

        @if ($courses->isNotEmpty())
            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($courses as $course)
                    <li>
                        <x-trafikrak::courses.summary :course="$course"/>
                    </li>
                @endforeach
            </ul>

            {{ $courses->links() }}
        @else
            <p>{{ __('No hay resultados.') }}</p>
        @endif
    </div>
</article>
