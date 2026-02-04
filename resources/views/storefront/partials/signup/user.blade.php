@if (Auth::check())
    @if ($billing->customerAddresses->isNotEmpty())
        <div class="mb-6">
            <x-numaxlab-atomic::atoms.select
                    wire:model.live="billing.customer_address_id"
                    name="billing.customer_address_id"
                    id="billing.customer_address_id"
                    label="{{ __('Tus direcciones') }}"
            >
                <option value="">{{ __('Selecciona una de tus direcciones') }}</option>
                @foreach ($billing->customerAddresses as $address)
                    <option value="{{ $address->id }}"
                            wire:key="{{ 'customer-address-' . $address->id }}">
                        {{ $address->line_one }}, {{ $address->city }}
                    </option>
                @endforeach
            </x-numaxlab-atomic::atoms.select>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-numaxlab-atomic::atoms.input
                wire:model="billing.first_name"
                type="text"
                name="billing.first_name"
                id="billing.first_name"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('Nombre') }}"
        >
            {{ __('Nombre') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="billing.last_name"
                type="text"
                name="billing.last_name"
                id="billing.last_name"
                required
                autocomplete="last-name"
                placeholder="{{ __('Apellidos') }}"
        >
            {{ __('Apellidos') }}
        </x-numaxlab-atomic::atoms.input>

        @include('testa::storefront.partials.signup.billing-address-fields')
    </div>
@else
    @include('testa::storefront.partials.checkout.embed-auth')

    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        @include('testa::storefront.partials.signup.billing-address-fields')
    </div>
@endif
