<x-numaxlab-atomic::organisms.tier class="mt-7">
    <x-numaxlab-atomic::organisms.tier.header>
        <h2 class="at-heading is-2">
            {{ $type == 'shipping' ? __('Datos de envío') : __('Datos de facturación') }}
        </h2>

        @if ($currentStep > $step)
            <x-numaxlab-atomic::atoms.button
                    type="button"
                    class="is-secondary at-small"
                    wire:click.prevent="$set('currentStep', {{ $step }})">
                {{ __('Modificar') }}
            </x-numaxlab-atomic::atoms.button>
        @endif
    </x-numaxlab-atomic::organisms.tier.header>
    <form wire:submit="saveAddress('{{ $type }}')">
        @if ($type == 'shipping' && $step == $currentStep)
            <x-numaxlab-atomic::atoms.forms.checkbox wire:model.live="shippingIsBilling" id="shippingIsBilling">
                {{ __('Usar los mismos datos para facturación') }}
            </x-numaxlab-atomic::atoms.forms.checkbox>
        @endif

        @if ($currentStep >= $step)
            @if ($step == $currentStep)
                <div class="flex flex-col gap-6 mt-6">
                    @if ($$type->customerAddresses->isNotEmpty())
                        <x-numaxlab-atomic::atoms.select
                                wire:model.live="{{ $type }}.customer_address_id"
                                name="{{ $type }}.customer_address_id"
                                id="{{ $type }}.customer_address_id"
                                label="{{ __('Tus direcciones') }}"
                        >
                            <option value="">{{ __('Selecciona una de tus direcciones') }}</option>
                            @foreach ($$type->customerAddresses as $address)
                                <option value="{{ $address->id }}"
                                        wire:key="{{ $type . '-customer-address-' . $address->id }}">
                                    {{ $address->line_one }}, {{ $address->city }}
                                </option>
                            @endforeach
                        </x-numaxlab-atomic::atoms.select>
                    @endif

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.first_name"
                            type="text"
                            name="{{ $type }}.first_name"
                            id="{{ $type }}.first_name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="{{ __('Nombre') }}"
                    >
                        {{ __('Nombre') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.last_name"
                            type="text"
                            name="{{ $type }}.last_name"
                            id="{{ $type }}.last_name"
                            required
                            autocomplete="last-name"
                            placeholder="{{ __('Apellidos') }}"
                    >
                        {{ __('Apellidos') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.company_name"
                            type="text"
                            name="{{ $type }}.company_name"
                            id="{{ $type }}.company_name"
                            placeholder="{{ __('Nombre de empresa') }}"
                    >
                        {{ __('Nombre de empresa') }}
                    </x-numaxlab-atomic::atoms.input>


                    @if ($type === 'billing')
                        <x-numaxlab-atomic::atoms.input
                                wire:model="{{ $type }}.tax_identifier"
                                type="text"
                                name="{{ $type }}.tax_identifier"
                                id="{{ $type }}.tax_identifier"
                                placeholder="{{ __('NIF/CIF') }}"
                        >
                            {{ __('NIF/CIF') }}
                        </x-numaxlab-atomic::atoms.input>
                    @endif

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.contact_phone"
                            type="text"
                            name="{{ $type }}.contact_phone"
                            id="{{ $type }}.contact_phone"
                            placeholder="{{ __('Teléfono de contacto') }}"
                    >
                        {{ __('Teléfono de contacto') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.contact_email"
                            type="email"
                            name="{{ $type }}.contact_email"
                            id="{{ $type }}.contact_email"
                            placeholder="{{ __('Email de contacto') }}"
                    >
                        {{ __('Email de contacto') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.select
                            wire:model.live="{{ $type }}.country_id"
                            name="{{ $type }}.country_id"
                            id="{{ $type }}.country_id"
                            label="{{ __('País') }}"
                    >
                        <option value="">Selecciona un país</option>
                        @foreach ($$type->countries as $country)
                            <option value="{{ $country->id }}" wire:key="{{ $type . '-country-' . $country->id }}">
                                {{ $country->native }}
                            </option>
                        @endforeach
                    </x-numaxlab-atomic::atoms.select>

                    <x-numaxlab-atomic::atoms.select
                            wire:model="{{ $type }}.state"
                            name="{{ $type }}.state"
                            id="{{ $type }}.state"
                            label="{{ __('Provincia') }}"
                    >
                        <option value="">{{ __('Selecciona una provincia') }}</option>
                        @foreach($$type->states as $state)
                            <option value="{{ $state->name }}">{{ $state->name }}</option>
                        @endforeach
                    </x-numaxlab-atomic::atoms.select>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.city"
                            type="text"
                            name="{{ $type }}.city"
                            id="{{ $type }}.city"
                            required
                            placeholder="{{ __('Ciudad') }}"
                    >
                        {{ __('Ciudad') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.postcode"
                            type="text"
                            name="{{ $type }}.postcode"
                            id="{{ $type }}.postcode"
                            required
                            placeholder="{{ __('Código postal') }}"
                    >
                        {{ __('Código postal') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.line_one"
                            type="text"
                            name="{{ $type }}.line_one"
                            id="{{ $type }}.line_one"
                            required
                            placeholder="{{ __('Línea de dirección 1') }}"
                    >
                        {{ __('Línea de dirección 1') }}
                    </x-numaxlab-atomic::atoms.input>

                    <x-numaxlab-atomic::atoms.input
                            wire:model="{{ $type }}.line_two"
                            type="text"
                            name="{{ $type }}.line_two"
                            id="{{ $type }}.line_two"
                            placeholder="{{ __('Línea de dirección 2') }}"
                    >
                        {{ __('Línea de dirección 2') }}
                    </x-numaxlab-atomic::atoms.input>

                    @if (!$$type->customer_address_id)
                        <x-numaxlab-atomic::atoms.forms.checkbox
                                wire:model.live="{{ $type }}.saveToUser"
                                id="{{ $type }}.saveToUser"
                        >
                            {{ __('Guardar para futuros pedidos') }}
                        </x-numaxlab-atomic::atoms.forms.checkbox>
                    @endif

                    @if ($type === 'billing')
                        <x-numaxlab-atomic::atoms.forms.checkbox
                                wire:model.live="wantsInvoice"
                                id="wantsInvoice"
                        >
                            {{ __('Quiero factura') }}
                        </x-numaxlab-atomic::atoms.forms.checkbox>
                    @endif

                    <x-testa::loading-button
                            target="saveAddress"
                            loadingText="{{ __('Guardando...') }}"
                            class="is-primary"
                            wire:key="{{ $type }}-submit-button">
                        {{ __('Continuar') }}
                    </x-testa::loading-button>
                </div>
            @elseif($currentStep > $step)
                <dl class="grid grid-cols-1 gap-8 text-sm sm:grid-cols-2">
                    <div>
                        <div class="space-y-4">
                            <div>
                                <dt class="font-bold">
                                    {{ __('Nombre') }}
                                </dt>
                                <dd>
                                    {{ $this->{$type}->first_name }} {{ $this->{$type}->last_name }}
                                </dd>
                                @if ($this->{$type}->company_name)
                                    <dd>
                                        {{ $this->{$type}->company_name }}
                                    </dd>
                                @endif
                                @if ($this->{$type}->tax_identifier && $type === 'shipping')
                                    <dd>
                                        {{ $this->{$type}->tax_identifier }}
                                    </dd>
                                @endif
                            </div>

                            <div>
                                <dt class="font-bold">
                                    {{ __('Datos de contacto') }}
                                </dt>
                                <dd>
                                    {{ $this->{$type}->contact_email }}
                                </dd>
                                @if ($this->{$type}->contact_phone)
                                    <dd>
                                        {{ $this->{$type}->contact_phone }}
                                    </dd>
                                @endif
                            </div>

                            @if ($type === 'billing')
                                <div>
                                    <dt class="font-bold">
                                        {{ __('Factura') }}
                                    </dt>
                                    <dd>
                                        {{ $this->wantsInvoice ? __('Sí') : __('No') }}
                                    </dd>
                                    @if ($this->{$type}->tax_identifier)
                                        <dd>
                                            {{ $this->{$type}->tax_identifier }}
                                        </dd>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <dt class="font-bold">
                            {{ __('Dirección') }}
                        </dt>
                        <dd>
                            {{ $this->{$type}->line_one }}<br>
                            @if ($this->{$type}->line_two)
                                {{ $this->{$type}->line_two }}<br>
                            @endif
                            @if ($this->{$type}->city)
                                {{ $this->{$type}->city }}<br>
                            @endif
                            @if ($this->{$type}->state)
                                {{ $this->{$type}->state }}<br>
                            @endif
                            {{ $this->{$type}->postcode }}<br>
                        </dd>
                    </div>
                </dl>
            @endif
        @else
            <p>{{ __('Primero necesitamos tus datos de envío.') }}</p>
        @endif
    </form>
</x-numaxlab-atomic::organisms.tier>