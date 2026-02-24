<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('dashboard') }}">
                    {{ __('Mi cuenta') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            @if ($form->address)
                {{ __('Editar dirección') }}
            @else
                {{ __('Añadir dirección') }}
            @endif
        </h1>
    </header>

    <form wire:submit="save" class="flex flex-col gap-6 mt-10">
        <x-numaxlab-atomic::atoms.input
                wire:model="form.first_name"
                type="text"
                name="form.first_name"
                id="form.first_name"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('Nombre') }}"
        >
            {{ __('Nombre') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="form.last_name"
                type="text"
                name="form.last_name"
                id="form.last_name"
                required
                autocomplete="last-name"
                placeholder="{{ __('Apellidos') }}"
        >
            {{ __('Apellidos') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="form.company_name"
                type="text"
                name="form.company_name"
                id="form.company_name"
                placeholder="{{ __('Nombre de la empresa') }}"
        >
            {{ __('Nombre de la empresa') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="form.tax_identifier"
                type="text"
                name="form.tax_identifier"
                id="form.tax_identifier"
                placeholder="{{ __('NIF/CIF') }}"
        >
            {{ __('NIF/CIF') }}
        </x-numaxlab-atomic::atoms.input>

        <div class="flex flex-col gap-6 md:flex-row">
            <div class="md:w-1/2">
                <x-numaxlab-atomic::atoms.select
                        wire:model.live="form.country_id"
                        name="form.country_id"
                        id="form.country_id"
                        label="{{ __('País') }}"
                >
                    <option value="">{{ __('Selecciona un país') }}</option>
                    @foreach ($form->countries as $country)
                        <option value="{{ $country->id }}">{{ $country->native }}</option>
                    @endforeach
                </x-numaxlab-atomic::atoms.select>
            </div>

            <div class="md:w-1/2">
                <x-numaxlab-atomic::atoms.select
                        wire:model="form.state"
                        name="form.state"
                        id="form.state"
                        label="{{ __('Provincia') }}"
                >
                    <option value="">{{ __('Selecciona una provincia') }}</option>
                    @foreach($form->states as $state)
                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                    @endforeach
                </x-numaxlab-atomic::atoms.select>
            </div>
        </div>

        <div class="flex flex-col gap-6 md:flex-row">
            <div class="md:w-1/2">
                <x-numaxlab-atomic::atoms.input
                        wire:model="form.postcode"
                        type="text"
                        name="form.postcode"
                        id="form.postcode"
                        required
                        placeholder="{{ __('Código postal') }}"
                >
                    {{ __('Código postal') }}
                </x-numaxlab-atomic::atoms.input>
            </div>

            <div class="md:w-1/2">
                <x-numaxlab-atomic::atoms.input
                        wire:model="form.city"
                        type="text"
                        name="form.city"
                        id="form.city"
                        required
                        placeholder="{{ __('Ciudad') }}"
                >
                    {{ __('Ciudad') }}
                </x-numaxlab-atomic::atoms.input>
            </div>
        </div>

        <x-numaxlab-atomic::atoms.input
                wire:model="form.line_one"
                type="text"
                name="form.line_one"
                id="form.line_one"
                required
                placeholder="{{ __('Línea de dirección 1') }}"
        >
            {{ __('Línea de dirección 1') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="form.line_two"
                type="text"
                name="form.line_two"
                id="form.line_two"
                placeholder="{{ __('Línea de dirección 2') }}"
        >
            {{ __('Línea de dirección 2') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.forms.checkbox
                wire:model="form.shipping_default"
                value="1"
                name="form.shipping_default"
                id="form.shipping_default"
        >
            {{ __('Es mi dirección principal de envío') }}
        </x-numaxlab-atomic::atoms.forms.checkbox>

        <x-numaxlab-atomic::atoms.forms.checkbox
                wire:model="form.billing_default"
                value="1"
                name="form.billing_default"
                id="form.billing_default"
        >
            {{ __('Es mi dirección principal de facturación') }}
        </x-numaxlab-atomic::atoms.forms.checkbox>

        <x-numaxlab-atomic::atoms.button type="submit" class="is-primary w-full">
            {{ __('Guardar') }}
        </x-numaxlab-atomic::atoms.button>
    </form>
</article>