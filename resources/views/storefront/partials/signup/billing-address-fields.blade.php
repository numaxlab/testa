<x-numaxlab-atomic::atoms.input
        wire:model="billing.company_name"
        type="text"
        name="billing.company_name"
        id="billing.company_name"
        placeholder="{{ __('Nombre de la empresa') }}"
>
    {{ __('Nombre de la empresa') }}
</x-numaxlab-atomic::atoms.input>

<x-numaxlab-atomic::atoms.input
        wire:model="billing.contact_phone"
        type="text"
        name="billing.contact_phone"
        id="billing.contact_phone"
        placeholder="{{ __('Teléfono de contacto') }}"
>
    {{ __('Teléfono de contacto') }}
</x-numaxlab-atomic::atoms.input>

@if (Auth::check())
    <x-numaxlab-atomic::atoms.input
            wire:model="billing.contact_email"
            type="email"
            name="billing.contact_email"
            id="billing.contact_email"
            placeholder="{{ __('Email de contacto') }}"
    >
        {{ __('Email de contacto') }}
    </x-numaxlab-atomic::atoms.input>
@endif

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
