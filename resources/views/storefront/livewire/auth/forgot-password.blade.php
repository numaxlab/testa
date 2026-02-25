<x-slot name="bodyClass">bg-secondary</x-slot>

<section class="flex flex-col gap-6 sm:w-sm sm:mx-auto">
    <h1 class="at-heading is-1">{{ __('Recuperar contraseña') }}</h1>

    <p>
        Indica el correo electrónico con el que te registraste para recuperar tu contraseña. Si no lo recuerdas,
        <a href="">contacta con la tienda</a>.
    </p>

    <x-testa::auth.session-status class="text-center" :status="session('status')"/>

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <x-numaxlab-atomic::atoms.input
                wire:model="email"
                type="email"
                name="email"
                id="email"
                placeholder="email@ejemplo.com"
                required
                autofocus
                autocomplete="email"
        >
            {{ __('Correo electrónico') }}
        </x-numaxlab-atomic::atoms.input>

        <x-testa::loading-button target="sendPasswordResetLink" class="is-primary w-full">
            {{ __('Recuperar contraseña') }}
        </x-testa::loading-button>
    </form>

    <p>
        {{ __('Volver a') }}
        <a href="{{ route('login') }}" wire:navigate>{{ __('iniciar sesión') }}</a>.
    </p>
</section>
