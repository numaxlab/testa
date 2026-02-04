@if (!Auth::check())
    <p>
        {{ __('¿Ya tienes cuenta?') }}
        <button wire:click="redirectToLogin" class="text-primary">
            {{ __('Iniciar sesión') }}
        </button>
    </p>

    <div class="mt-4">
        <p>{{ __('¿No tienes cuenta de usuaria? Regístrate ahora') }}</p>

        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
            @include('testa::storefront.partials.auth.register-form')
        </div>
    </div>
@endif