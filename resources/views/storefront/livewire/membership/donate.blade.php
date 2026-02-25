<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header>
        <h1 class="at-heading is-1">{{ __('Donación') }}</h1>

        <form wire:submit="donate" class="mt-10">
            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('¿Qué cantidad quieres aportar?') }}
                </legend>

                <ul class="flex flex-wrap items-center gap-8 mt-20 mb-5 clear-both">
                    @foreach ($this->quantities as $quantity)
                        <li wire:key="qty-{{ $quantity['id'] }}" class="md:w-1/6">
                            <input type="radio" name="qty" id="qty-{{ $quantity['id'] }}"
                                   wire:model.live="selectedQuantity"
                                   value="{{ $quantity['id'] }}">
                            <label class="text-xl" for="qty-{{ $quantity['id'] }}">
                                {{ $quantity['pricing']->priceIncTax()->formatted() }}
                            </label>
                        </li>
                    @endforeach

                    <li wire:key="qty-free" class="md:w-2/6">
                        <div class="flex items-center gap-4">
                            <div class="w-1/2">
                                <input type="radio" name="qty" id="qty-free" wire:model.live="selectedQuantity"
                                       value="free">
                                <label class="text-xl" for="qty-free">
                                    {{ __('Yo eligo') }}
                                </label>
                            </div>
                            @if ($selectedQuantity === 'free')
                                <div class="w-1/2 -mt-6">
                                    <x-numaxlab-atomic::atoms.input
                                            wire:model="freeQuantityValue"
                                            type="number"
                                            name="free-qty-value"
                                            id="free-qty-value"
                                            min="1"
                                    >
                                        {{ __('Mi aportación') }}
                                    </x-numaxlab-atomic::atoms.input>
                                </div>
                            @endif
                        </div>
                    </li>
                </ul>

                <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('selectedQuantity')"/>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('Tus datos') }}
                </legend>

                <div class="mt-15 clear-both">
                    @include('testa::storefront.partials.donation.user')
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('Método de pago') }}
                </legend>

                <div class="mt-15 clear-both">
                    @include('testa::storefront.partials.checkout.payment')
                </div>
            </fieldset>

            <div class="mb-8">
                @include('testa::storefront.partials.privacy-policy')
            </div>

            <x-testa::loading-button target="donate" class="is-primary w-full">
                {{ __('Donar') }}
            </x-testa::loading-button>
        </form>
    </header>
</article>