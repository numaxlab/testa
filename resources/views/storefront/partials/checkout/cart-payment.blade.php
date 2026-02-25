<x-numaxlab-atomic::organisms.tier class="mt-7">
    <x-numaxlab-atomic::organisms.tier.header>
        <h2 class="at-heading is-2">
            {{ __('Modo de pago') }}
        </h2>
    </x-numaxlab-atomic::organisms.tier.header>

    @if ($currentStep >= $step)
        <ul class="grid gap-5 md:grid-cols-2">
            @foreach ($paymentTypes as $type)
                <li wire:key="paymentType{{ $type }}">
                    <x-numaxlab-atomic::atoms.forms.radio
                            wire:model.live="paymentType"
                            id="paymentType-{{ $type }}"
                            name="payment_type"
                            value="{{ $type }}">
                        <span class="text-2xl">
                            {{ __("testa::global.payment_types.{$type}.title") }}
                        </span>
                    </x-numaxlab-atomic::atoms.forms.radio>

                    <p class="at-small mt-2">
                        {{ __("testa::global.payment_types.{$type}.description") }}
                    </p>
                </li>
            @endforeach
        </ul>

        @error('paymentType')
        <p class="text-danger text-sm mt-2">{{ $message }}</p>
        @enderror
    @else
        <p>{{ __('Primero necesitamos tus datos de facturación.') }}</p>
    @endif
</x-numaxlab-atomic::organisms.tier>
