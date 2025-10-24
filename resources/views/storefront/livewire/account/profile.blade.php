<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('dashboard') }}">
                    {{ __('Mi cuenta') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">{{ __('Gestionar cuenta') }}</h1>
    </header>
    <form wire:submit="updateProfileInformation" class="flex flex-col gap-6">
        <x-numaxlab-atomic::atoms.input
                wire:model="first_name"
                type="text"
                name="first_name"
                id="first_name"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('Nombre') }}"
        >
            {{ __('Nombre') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="last_name"
                type="text"
                name="last_name"
                id="last_name"
                required
                autocomplete="last-name"
                placeholder="{{ __('Apellidos') }}"
        >
            {{ __('Apellidos') }}
        </x-numaxlab-atomic::atoms.input>

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

        <x-numaxlab-atomic::atoms.input
                wire:model="tax_identifier"
                type="text"
                name="tax_identifier"
                id="tax_identifier"
                placeholder="{{ __('NIF') }}"
        >
            {{ __('NIF') }}
        </x-numaxlab-atomic::atoms.input>

        <x-numaxlab-atomic::atoms.input
                wire:model="company_name"
                type="text"
                name="company_name"
                id="company_name"
                placeholder="{{ __('Nombre de empresa') }}"
        >
            {{ __('Nombre de empresa') }}
        </x-numaxlab-atomic::atoms.input>

        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
            <div>
                <p class="mt-4">
                    {{ __('Tu cuenta de correo electrónico no está verificado.') }}

                    <a class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                        {{ __('Haz clic aquí para reenviar el email de verificación.') }}
                    </a>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                        {{ __('Un nuevo email de verificación ha sido enviado, revisa tu buzón de correo electrónico.') }}
                    </p>
                @endif
            </div>
        @endif

        <x-numaxlab-atomic::atoms.button type="submit" class="is-primary w-full">
            {{ __('Guardar') }}
        </x-numaxlab-atomic::atoms.button>

        <x-trafikrak::action-message class="me-3" on="profile-updated">
            {{ __('Guardado correctamente') }}
        </x-trafikrak::action-message>
    </form>

    <form wire:submit="deleteUser" class="space-y-6 mt-10">
        <h2 class="at-heading is-2">{{ __('Eliminar mi cuenta') }}</h2>

        <p>{{ __('Si eliminas tu cuenta todos los recursos y datos que tienes guardados serán eliminados de forma permanente. Por favor, introduce tu contraseña para confirmar que quieres borrar tus datos de forma permanente.') }}</p>

        <x-numaxlab-atomic::atoms.input
                wire:model="password"
                type="password"
                name="password"
                id="password"
                placeholder="{{ __('Contraseña') }}"
                required
        >
            {{ __('Contraseña') }}
        </x-numaxlab-atomic::atoms.input>

        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <x-numaxlab-atomic::atoms.button class="bg-danger text-white" type="submit">
                {{ __('Eliminar cuenta') }}
            </x-numaxlab-atomic::atoms.button>
        </div>
    </form>
</article>