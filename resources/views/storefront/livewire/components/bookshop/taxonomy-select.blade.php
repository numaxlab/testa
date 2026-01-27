<div x-data="{ isOpen: @entangle('isOpen') }" class="relative">

    <button @click="isOpen = !isOpen" type="button"
            class="relative w-full border border-black shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <span class="block truncate">{{ $selectedName }}</span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                 fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </span>
    </button>

    <div x-show="isOpen"
         @click.away="isOpen = false"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
         style="display: none;">
        <div class="p-2">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar..."
                   class="w-full px-2 py-1 border border-black focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        @if ($selectedId)
            <div wire:click="clearSelection"
                 class="cursor-pointer hover:bg-primary hover:text-white p-2 text-gray-500 border-b border-gray-200">
                Todas las materias
            </div>
        @endif

        @forelse ($options as $option)
            <div wire:click="selectOption('{{ $option->id }}', '{{ $option->translateAttribute('name') }}')"
                 class="cursor-pointer hover:bg-primary hover:text-white p-2">
                {{ $option->translateAttribute('name') }}
            </div>
        @empty
            @if (!$selectedId)
                <div class="p-2 text-gray-500">No se encontraron resultados.</div>
            @endif
        @endforelse
    </div>

    <input type="hidden" name="selected_id" value="{{ $selectedId }}">
</div>