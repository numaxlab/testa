@props([
    'target',
    'type' => 'submit',
    'loadingText' => null,
    'disabled' => false,
])

<x-numaxlab-atomic::atoms.button
        :type="$type"
        :disabled="$disabled"
        wire:loading.attr="disabled"
        wire:target="{{ $target }}"
        {{ $attributes }}
>
    <span wire:loading.remove wire:target="{{ $target }}">{{ $slot }}</span>

    <span wire:loading wire:target="{{ $target }}" class="inline-flex items-center gap-2">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        @if ($loadingText)
            <span>{{ $loadingText }}</span>
        @endif
        <span class="sr-only">{{ __('Cargando...') }}</span>
    </span>
</x-numaxlab-atomic::atoms.button>
