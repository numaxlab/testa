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

    <x-numaxlab-atomic::atoms.input
            wire:model="billing.tax_identifier"
            type="text"
            name="billing.tax_identifier"
            id="billing.tax_identifier"
            placeholder="{{ __('DNI/NIF') }}"
    >
        {{ __('DNI/NIF') }}
    </x-numaxlab-atomic::atoms.input>

    <x-numaxlab-atomic::atoms.input
            wire:model="billing.company_name"
            type="text"
            name="billing.company_name"
            id="billing.company_name"
            placeholder="{{ __('Nombre de la empresa') }}"
    >
        {{ __('Nombre de la empresa') }}
    </x-numaxlab-atomic::atoms.input>

    <div class="flex flex-col gap-6 md:flex-row md:col-span-2">
        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.input
                    wire:model="billing.contact_phone"
                    type="text"
                    name="billing.contact_phone"
                    id="billing.contact_phone"
                    placeholder="{{ __('Teléfono de contacto') }}"
            >
                {{ __('Teléfono de contacto') }}
            </x-numaxlab-atomic::atoms.input>
        </div>
        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.input
                    wire:model="billing.contact_email"
                    type="email"
                    name="billing.contact_email"
                    id="billing.contact_email"
                    placeholder="{{ __('Email de contacto') }}"
            >
                {{ __('Email de contacto') }}
            </x-numaxlab-atomic::atoms.input>
        </div>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:col-span-2">
        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.select
                    wire:model.live="billing.country_id"
                    name="billing.country_id"
                    id="billing.country_id"
                    label="{{ __('País') }}"
            >
                <option value="">{{ __('Selecciona un país') }}</option>
                @foreach ($billing->countries as $country)
                    <option value="{{ $country->id }}">{{ $country->native }}</option>
                @endforeach
            </x-numaxlab-atomic::atoms.select>
        </div>

        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.select
                    wire:model="billing.state"
                    name="billing.state"
                    id="billing.state"
                    label="{{ __('Provincia') }}"
            >
                <option value="">{{ __('Selecciona una provincia') }}</option>
                @foreach($billing->states as $state)
                    <option value="{{ $state->name }}">{{ $state->name }}</option>
                @endforeach
            </x-numaxlab-atomic::atoms.select>
        </div>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:col-span-2">
        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.input
                    wire:model="billing.postcode"
                    type="text"
                    name="billing.postcode"
                    id="billing.postcode"
                    required
                    placeholder="{{ __('Código postal') }}"
            >
                {{ __('Código postal') }}
            </x-numaxlab-atomic::atoms.input>
        </div>

        <div class="md:w-1/2">
            <x-numaxlab-atomic::atoms.input
                    wire:model="billing.city"
                    type="text"
                    name="billing.city"
                    id="billing.city"
                    required
                    placeholder="{{ __('Ciudad') }}"
            >
                {{ __('Ciudad') }}
            </x-numaxlab-atomic::atoms.input>
        </div>
    </div>

    <x-numaxlab-atomic::atoms.input
            wire:model="billing.line_one"
            type="text"
            name="billing.line_one"
            id="billing.line_one"
            required
            placeholder="{{ __('Línea de dirección 1') }}"
    >
        {{ __('Línea de dirección 1') }}
    </x-numaxlab-atomic::atoms.input>

    <x-numaxlab-atomic::atoms.input
            wire:model="billing.line_two"
            type="text"
            name="billing.line_two"
            id="billing.line_two"
            placeholder="{{ __('Línea de dirección 2') }}"
    >
        {{ __('Línea de dirección 2') }}
    </x-numaxlab-atomic::atoms.input>

    <div class="md:col-span-2">
        <x-numaxlab-atomic::atoms.forms.checkbox
                wire:model="billing.save_address"
                name="billing.save_address"
                value="1"
                id="billing.save_address"
        >
            <span class="text-lg">{{ __('Guardar para futuras compras') }}</span>
        </x-numaxlab-atomic::atoms.forms.checkbox>
    </div>
</div>