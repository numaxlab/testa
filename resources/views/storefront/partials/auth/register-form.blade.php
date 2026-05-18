<x-numaxlab-atomic::atoms.input
        wire:model="first_name"
        type="text"
        name="first_name"
        id="first_name"
        required
        autofocus
        autocomplete="name"
        :placeholder="__('Nombre')"
>
    {{ __('Nombre') }}
</x-numaxlab-atomic::atoms.input>

<x-numaxlab-atomic::atoms.input
        wire:model="last_name"
        type="text"
        name="last_name"
        id="last_name"
        required
        autofocus
        autocomplete="last-name"
        :placeholder="__('Apellidos')"
>
    {{ __('Apellidos') }}
</x-numaxlab-atomic::atoms.input>

<div class="md:col-span-2">
    <x-numaxlab-atomic::atoms.input
            wire:model="email"
            type="email"
            name="email"
            id="email"
            placeholder="email@ejemplo.com"
            required
            autocomplete="email"
    >
        {{ __('Correo electrónico') }}
    </x-numaxlab-atomic::atoms.input>
</div>

<x-testa::password-input
        wire:model="password"
        name="password"
        id="password"
        placeholder="{{ __('Contraseña') }}"
        required
        autocomplete="new-password"
>
    {{ __('Contraseña') }}
</x-testa::password-input>

<x-testa::password-input
        wire:model="password_confirmation"
        name="password_confirmation"
        id="password-confirmation"
        placeholder="{{ __('Confirmar contraseña') }}"
        required
        autocomplete="new-password"
>
    {{ __('Confirmar contraseña') }}
</x-testa::password-input>