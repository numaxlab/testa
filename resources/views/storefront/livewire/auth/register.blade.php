<x-slot name="bodyClass">bg-secondary</x-slot>

<section class="flex flex-col gap-6 lg:w-4xl lg:mx-auto">
    <h1 class="at-heading is-1">{{ __('Regístrate') }}</h1>

    <x-testa::auth.session-status class="text-center" :status="session('status')"/>

    <form wire:submit="register" class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @include('testa::storefront.partials.auth.register-form')

        <div class="md:col-span-2">
            @include('testa::storefront.partials.privacy-policy')
        </div>

        <x-testa::loading-button target="register" class="is-primary w-full md:col-span-2">
            {{ __('Crear cuenta') }}
        </x-testa::loading-button>

        <p class="md:col-span-2">
            {{ __('Volver a') }}
            <a href="{{ route('login') }}" wire:navigate>{{ __('iniciar sesión') }}</a>.
        </p>
    </form>
</section>