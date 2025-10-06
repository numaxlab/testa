<article class="container mx-auto px-4 lg:max-w-4xl">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.membership.homepage') }}" wire:navigate>
                    {{ __('Apoya el proyecto') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">Asóciate</h1>

        <form wire:submit="signup" class="mt-10">
            <fieldset class="mb-8 pt-2 border-t">
                <legend class="at-heading is-2 float-left w-full">¿En qué modalidad te quieres asociar?</legend>

                <ul class="grid gap-8 md:grid-cols-2 mt-15 clear-both">
                    @foreach ($tiers as $tier)
                        <li wire:key="tier-{{ $tier->id }}">
                            <input type="radio" name="tier" id="tier{{ $tier->id }}" wire:model.live="selectedTier"
                                   value="{{ $tier->id }}">
                            <label class="font-bold" for="tier{{ $tier->id }}">
                                {{ $tier->name }}
                            </label>
                            @if ($tier->description)
                                <div class="at-small mt-2">
                                    {!! $tier->description !!}
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </fieldset>

            @if ($plans->isNotEmpty())
                <fieldset class="mb-8 pt-2 border-t">
                    <legend class="at-heading is-2 float-left w-full">Opciones</legend>

                    <ul class="grid gap-8 md:grid-cols-2 mt-15 clear-both">
                        @foreach ($plans as $plan)
                            <li wire:key="plan-{{ $plan->id }}">
                                <input type="radio" name="plan" id="plan{{ $plan->id }}" wire:model.live="selectedPlan"
                                       value="{{ $plan->id }}">
                                <label class="font-bold" for="plan{{ $plan->id }}">
                                    {{ $plan->name }}

                                    (
                                    <livewire:trafikrak.storefront.livewire.components.price
                                            :key="'price-' . $plan->id"
                                            :purchasable="$plan"/>
                                    )
                                </label>
                                @if ($plan->description)
                                    <div class="at-small mt-2">
                                        {!! $plan->description !!}
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
            @endif

            <fieldset class="mb-8 pt-2 border-t">
                <legend class="at-heading is-2 float-left w-full">Tus datos</legend>

                <div class="mt-15 clear-both">
                    @if (Auth::check())
                        @if ($billing->customerAddresses->isNotEmpty())
                            <div class="mb-6">
                                <x-numaxlab-atomic::atoms.select
                                        wire:model.live="billing.customer_address_id"
                                        name="billing.customer_address_id"
                                        id="billing.customer_address_id"
                                        label="{{ __('Tus direcciones') }}"
                                >
                                    <option value="">Selecciona una de tus direcciones</option>
                                    @foreach ($billing->customerAddresses as $address)
                                        <option value="{{ $address->id }}"
                                                wire:key="{{ 'customer-address-' . $address->id }}">
                                            {{ $address->line_one }}, {{ $address->city }}
                                        </option>
                                    @endforeach
                                </x-numaxlab-atomic::atoms.select>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <x-numaxlab-atomic::atoms.input
                                    wire:model="billing.first_name"
                                    type="text"
                                    name="billing.first_name"
                                    id="billing.first_name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="{{ __('Nombre') }}"
                            >
                                {{ __('Nombre') }}
                            </x-numaxlab-atomic::atoms.input>

                            <x-numaxlab-atomic::atoms.input
                                    wire:model="billing.last_name"
                                    type="text"
                                    name="billing.last_name"
                                    id="billing.last_name"
                                    required
                                    autocomplete="last-name"
                                    placeholder="{{ __('Apellidos') }}"
                            >
                                {{ __('Apellidos') }}
                            </x-numaxlab-atomic::atoms.input>

                            <x-numaxlab-atomic::atoms.input
                                    wire:model="billing.company_name"
                                    type="text"
                                    name="billing.company_name"
                                    id="billing.company_name"
                                    placeholder="{{ __('Nombre de la empresa') }}"
                            >
                                {{ __('Nombre de la empresa') }}
                            </x-numaxlab-atomic::atoms.input>

                            <div class="flex flex-col gap-6 md:flex-row">
                                <div class="md:w-1/2">
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
                                </div>

                                <div class="md:w-1/2">
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
                                </div>
                            </div>

                            <div class="flex flex-col gap-6 md:flex-row">
                                <div class="md:w-1/2">
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
                                </div>

                                <div class="md:w-1/2">
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
                                </div>
                            </div>

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
                        </div>
                    @else
                        <a href="{{ route('login') }}" wire:navigate>{{ __('Iniciar sesión') }}</a>

                        <p>
                            {{ __('¿No tienes cuenta de usuaria?') }}<br>
                            <a href="{{ route('register') }}" wire:navigate>{{ __('Regístrate aquí') }}</a>
                        </p>
                    @endif
                </div>
            </fieldset>

            <fieldset class="mb-8 pt-2 border-t">
                <legend class="at-heading is-2 float-left w-full">Método de pago</legend>
            </fieldset>

            <button class="at-button is-primary w-full" type="submit">
                Asociarme
            </button>
        </form>
    </header>
</article>