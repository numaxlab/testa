<div class="mt-3">
    @if ($displayPrice && $pricing)
        <button
                class="at-button w-full"
                :class="{ 'text-primary border-primary': !hover, 'is-primary': hover }"
                wire:click.prevent="addToCart"
                wire:loading.attr="disabled"
                wire:target="addToCart"
                x-data="{hover: false}"
                @mouseover="hover = true"
                @mouseout="hover = false"
        >
            <span wire:loading.remove wire:target="addToCart">
                <i class="icon icon-shopping-bag" aria-hidden="true"></i>
                @if($pricing)
                    <span x-show="!hover">{{ $pricing->priceIncTax()->formatted() }}</span>
                @endif
                <span x-show="hover">{{ __('Comprar') }}</span>
            </span>
            <span wire:loading wire:target="addToCart" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="sr-only">{{ __('Cargando...') }}</span>
            </span>
        </button>
    @else
        <x-testa::loading-button target="addToCart" class="is-primary w-full">
            {{ __('Comprar') }}
        </x-testa::loading-button>
    @endif

    @if ($errors->has('quantity'))
        <div class="ml-alert is-danger mt-4 text-xs" role="alert">
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
