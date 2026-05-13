<div>
    <ul class="grid gap-5 md:grid-cols-3">
        <li>
            <x-numaxlab-atomic::atoms.forms.radio
                    wire:model.live="shippingMethod"
                    id="shippingMethod-send"
                    key="shippingMethodSend"
                    name="shipping_method"
                    checked
                    value="send"
            >
                        <span class="text-2xl">
                            {{ __('Envío a domicilio') }}
                        </span>
            </x-numaxlab-atomic::atoms.forms.radio>

            <p class="at-small mt-2">
                {{ app(\Testa\Settings\TextSettings::class)->getShippingHomeDescription() }}
            </p>
        </li>
        @foreach ($this->pickupOptions as $shippingMethod)
            <li wire:key="shippingMethod-{{ $shippingMethod->id }}">
                <x-numaxlab-atomic::atoms.forms.radio
                        wire:model.live="shippingMethod"
                        id="shippingMethod-{{ $shippingMethod->id }}"
                        name="shipping_method"
                        value="{{ $shippingMethod->id }}">
                        <span class="text-2xl">
                            {{ $shippingMethod->name }}
                        </span>
                </x-numaxlab-atomic::atoms.forms.radio>

                @if ($shippingMethod->description)
                    <div class="at-small mt-2">
                        {!! $shippingMethod->description !!}
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</div>