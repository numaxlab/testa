<x-slot name="bodyClass">bg-secondary</x-slot>

<div class="flex flex-col gap-6 px-100 sm:w-sm sm:mx-auto">
    <!-- Session Status -->
    <x-testa::auth.session-status class="text-center" :status="session('status')"/>

    <form wire:submit="confirmPassword" class="flex flex-col gap-6">
        <x-numaxlab-atomic::atoms.input
                wire:model="password"
                type="password"
                name="password"
                id="password"
                placeholder="{{ __('Password') }}"
                required
                autocomplete="new-password"
        >
            {{ __('Password') }}
        </x-numaxlab-atomic::atoms.input>

        <x-testa::loading-button target="confirmPassword" class="is-primary w-full">
            {{ __('Confirm') }}
        </x-testa::loading-button>
    </form>
</div>