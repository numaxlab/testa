<x-numaxlab-atomic::organisms.tier class="mt-7">
    <x-numaxlab-atomic::organisms.tier.header>
        <h2 class="at-heading is-2">
            {{ __('Modo de pago') }}
        </h2>
    </x-numaxlab-atomic::organisms.tier.header>

    @if ($currentStep >= $step)
        <ul class="space-y-5">
            @foreach ($paymentTypes as $type)
                <li>
                    <x-numaxlab-atomic::atoms.forms.radio
                            name="payment_type"
                            id="paymentType-{{ $type }}"
                            key="paymentType{{ $type }}"
                            value="{{ $type }}">
                        <span class="text-2xl">
                            {{ __("trafikrak::global.payment_types.{$type}") }}
                        </span>
                    </x-numaxlab-atomic::atoms.forms.radio>

                    <p class="at-small mt-2">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus congue tempus ultricies.
                        Curabitur tempus nulla a lectus tincidunt pulvinar. Aenean at metus ac lorem egestas tincidunt.
                    </p>
                </li>
            @endforeach
        </ul>
    @endif
</x-numaxlab-atomic::organisms.tier>
