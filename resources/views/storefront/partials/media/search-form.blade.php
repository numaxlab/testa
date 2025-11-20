<form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
    <div class="relative md:w-1/3">
        <x-numaxlab-atomic::atoms.forms.input
                type="search"
                wire:model="q"
                name="q"
                id="query"
                placeholder="{{ __('Buscar en audios y vídeos') }}"
                aria-label="{{ __('Buscar en audios y vídeos') }}"
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
            @foreach ($topics as $topic)
                <option value="{{ $topic->id }}" wire:key="topic-{{ $topic->id }}">
                    {{ $topic->name }}
                </option>
            @endforeach
        </x-numaxlab-atomic::atoms.forms.select>
    </div>
</form>