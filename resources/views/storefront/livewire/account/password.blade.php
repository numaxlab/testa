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
        <h1 class="at-heading is-1">{{ __('Modificar contraseña') }}</h1>
    </header>
    <form wire:submit="updatePassword" class="flex flex-col gap-6">
        <x-numaxlab-atomic::atoms.input
                wire:model="current_password"
                type="password"
                name="current_password"
                id="password"
                autocomplete="current-password"
                placeholder="{{ __('Contraseña actual') }}"
                required
        >
            {{ __('Contraseña actual') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="password"
                type="password"
                name="password"
                id="password"
                placeholder="{{ __('Nueva contraseña') }}"
                required
                autocomplete="new-password"
        >
            {{ __('Nueva contraseña') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="password_confirmation"
                type="password"
                name="password_confirmation"
                id="password-confirmation"
                placeholder="{{ __('Confirmar nueva contraseña') }}"
                required
                autocomplete="new-password"
        >
            {{ __('Confirmar nueva contraseña') }}
        </x-numaxlab-atomic::atoms.input>

        <x-testa::loading-button target="updatePassword" class="is-primary w-full">
            {{ __('Guardar') }}
        </x-testa::loading-button>

        <x-testa::action-message class="me-3" on="password-updated">
            {{ __('Contraseña modificada correctamente.') }}
        </x-testa::action-message>
    </form>
</article>