<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.news.homepage') }}">
                    {{ __('Actualidad') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Actividades') }}
        </h1>

        <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
            <div class="md:w-1/3">
                <x-numaxlab-atomic::atoms.forms.select
                        wire:model="l"
                        wire:change="search"
                        name="l"
                        id="location"
                        aria-label="{{ __('Filtrar por lugar') }}"
                >
                    <option value="">{{ __('Todos los lugares') }}</option>
                </x-numaxlab-atomic::atoms.forms.select>
            </div>

            <div class="md:w-1/3">
                <x-numaxlab-atomic::atoms.forms.select
                        wire:model="t"
                        wire:change="search"
                        name="t"
                        id="type"
                        aria-label="{{ __('Filtrar por tipo') }}"
                >
                    <option value="">{{ __('Todos los tipos') }}</option>
                    <option value="c">{{ __('Sesiones de cursos') }}</option>
                    @foreach ($eventTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </x-numaxlab-atomic::atoms.forms.select>
            </div>

            <div class="relative md:w-1/3">
                <x-numaxlab-atomic::atoms.forms.input
                        type="search"
                        wire:model="q"
                        name="q"
                        id="sectionQuery"
                        placeholder="{{ __('Buscar en actividades') }}"
                        aria-label="{{ __('Buscar en actividades') }}"
                        autocomplete="off"
                />
                <button type="submit" aria-label="{{ __('Buscar') }}" class="text-primary absolute inset-y-0 right-3">
                    <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </header>

    @if ($activities->isNotEmpty())
        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($activities as $activity)
                <li>
                    @if ($activity instanceof \Trafikrak\Models\News\Event)
                        <x-trafikrak::events.summary :event="$activity"/>
                    @elseif ($activity instanceof \Trafikrak\Models\Education\CourseModule)
                        <x-trafikrak::course-modules.activity :module="$activity"/>
                    @endif
                </li>
            @endforeach
        </ul>

        {{ $activities->links() }}
    @else
        <p>{{ __('No hay resultados.') }}</p>
    @endif
</article>