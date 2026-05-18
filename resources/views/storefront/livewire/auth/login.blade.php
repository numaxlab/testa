<x-slot name="bodyClass">bg-secondary</x-slot>

<section class="flex flex-col gap-6 sm:w-sm sm:mx-auto">
    <h1 class="at-heading is-1">{{ __('Iniciar sesión') }}</h1>

    @if (Route::has('register'))
        <p>
            {{ __('¿No tienes cuenta de usuaria?') }}<br>
            <a href="{{ route('register') }}" wire:navigate>{{ __('Regístrate aquí') }}</a>.
        </p>
    @endif

    <x-testa::auth.session-status class="text-center" :status="session('status')"/>

    <form wire:submit="login" class="flex flex-col gap-6">
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

        <x-testa::password-input
                wire:model="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
                :placeholder="__('Contraseña')"
        >
            {{ __('Contraseña') }}
            @if (Route::has('password.request'))
                <x-slot name="link">
                    <a class="at-small" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Olvidé mi contraseña') }}
                    </a>
                </x-slot>
            @endif
        </x-testa::password-input>

        <div>
            <x-numaxlab-atomic::atoms.forms.checkbox
                    wire:model="remember"
                    value="1"
                    id="remember-me"
            >
                {{ __('Acuérdate de mi') }}
            </x-numaxlab-atomic::atoms.forms.checkbox>
        </div>

        <x-testa::loading-button target="login" class="is-primary w-full">
            {{ __('Entrar') }}
        </x-testa::loading-button>
    </form>
</section>
