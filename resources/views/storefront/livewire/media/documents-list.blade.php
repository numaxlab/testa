<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.media.homepage') }}">
                    {{ __('Mediateca') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Documentos') }}
        </h1>

        <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
            <div class="relative md:w-1/3">
                <x-numaxlab-atomic::atoms.forms.input
                        type="search"
                        wire:model="q"
                        name="q"
                        id="sectionQuery"
                        placeholder="{{ __('Buscar en documentos') }}"
                        aria-label="{{ __('Buscar en documentos') }}"
                        autocomplete="off"
                />
                <button type="submit" aria-label="{{ __('Buscar') }}" class="text-primary absolute inset-y-0 right-3">
                    <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                </button>
            </div>

            <div class="md:w-1/3">
                <x-numaxlab-atomic::atoms.forms.select
                        wire:model="c"
                        wire:change="search"
                        name="c"
                        id="course"
                        aria-label="{{ __('Filtrar por curso') }}"
                >
                    <option value="">{{ __('Todos los cursos') }}</option>
                </x-numaxlab-atomic::atoms.forms.select>
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
                </x-numaxlab-atomic::atoms.forms.select>
            </div>
        </form>
    </header>

    @if ($documents->isNotEmpty())
        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($documents as $document)
                <li>
                    <x-trafikrak::documents.summary :media="$document" :href="Storage::url($document->path)"/>
                </li>
            @endforeach
        </ul>

        {{ $documents->links() }}
    @else
        <p>{{ __('No hay resultados.') }}</p>
    @endif
</article>