<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header>
        <h1 class="at-heading is-1">{{ __('Apoya el proyecto') }}</h1>
        <p class="mt-3">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quos, impedit? Corrupti voluptatibus officia nulla dolore commodi voluptatum, quas doloremque modi molestiae non fuga dolores natus atque odit consectetur porro minima. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quos, impedit? Corrupti voluptatibus officia nulla dolore commodi voluptatum, quas doloremque modi molestiae non fuga dolores natus atque odit consectetur porro minima.</p>

        <form wire:submit="signup" class="mt-10">
            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('¿En qué modalidad te quieres apoyar?') }}
                </legend>

                <ul class="grid gap-4 md:grid-cols-2 mt-15 mb-5 clear-both">
                @foreach ($tiers as $tier)
                    <li wire:key="tier-{{ $tier->id }}" class="list-none">
                        <input type="radio" 
                               name="tier" 
                               id="tier{{ $tier->id }}" 
                               wire:model.live="selectedTier"
                               value="{{ $tier->id }}"
                               class="sr-only peer">

                        <div class="cursor-pointer border rounded-lg h-full p-6 transition duration-150 relative 
                                    border-black bg-transparent text-gray-800 hover:border-[var(--color-primary)]
                                    peer-checked:border-[var(--color-primary)] peer-checked:bg-[var(--color-primary)] peer-checked:text-white"
                                    @click="$wire.selectedTier = '{{ $tier->id }}'"> 

                            <div class="flex flex-col">
                                <div class="flex items-start justify-between">
                                    <label class="text-2xl font-semibold cursor-pointer" for="tier{{ $tier->id }}">
                                        {{ $tier->name }}
                                    </label>
                                </div>
                                @if ($tier->description)
                                    <div class="mt-2 text-base opacity-90 peer-checked:text-white">
                                        {!! $tier->description !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
                </ul>

                <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('selectedTier')"/>
            </fieldset>

            @if ($plans->isNotEmpty())
                <fieldset class="mb-8">
                    <legend class="at-heading is-2 float-left w-full">
                        {{ __('Opciones') }}
                    </legend>

                    <ul class="grid gap-8 md:grid-cols-2 mt-15 mb-5 clear-both">
                        @foreach ($plans as $plan)
                            <li wire:key="plan-{{ $plan->id }}">
                                <input type="radio" name="plan" id="plan{{ $plan->id }}" wire:model.live="selectedPlan"
                                       value="{{ $plan->id }}">
                                <label class="text-2xl" for="plan{{ $plan->id }}">
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

                    <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('selectedPlan')"/>
                </fieldset>
            @endif

            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('Tus datos') }}
                </legend>

                <div class="mt-15 clear-both">
                    @include('trafikrak::storefront.partials.signup.user')
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('Método de pago') }}
                </legend>

                <div class="mt-15 clear-both">
                    @include('trafikrak::storefront.partials.signup.payment')
                </div>
            </fieldset>

            <div class="mb-8">
                @include('trafikrak::storefront.partials.privacy-policy')
            </div>

            <button class="at-button is-primary w-full" type="submit">
                {{ __('Asociarme') }}
            </button>
        </form>
    </header>
</article>