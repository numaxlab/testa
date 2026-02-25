<div>
    <x-numaxlab-atomic::organisms.tier>
        <x-numaxlab-atomic::organisms.tier.header class="flex gap-2">
            <h2 class="at-heading is-2">{{ __('Mis direcciones') }}</h2>
            <a href="{{ route('settings.add-address') }}" wire:navigate class="at-small at-button is-secondary">
                {{ __('Añadir') }}
            </a>
        </x-numaxlab-atomic::organisms.tier.header>

        @if ($addresses->isEmpty())
            <p>{{ __('Todavía no tienes direcciones guardadas.') }}</p>
        @else
            <ul class="space-y-3 divide-y divide-black">
                @foreach ($addresses as $address)
                    <li wire:key="address-{{ $address->id }}">
                        <address>
                            {{ $address->line_one }}<br>
                            {{ $address->postcode }} {{ $address->city }}
                        </address>
                        <ul class="flex gap-5 mt-2 mb-3">
                            <li>
                                <a href="{{ route('settings.edit-address', $address->id) }}" wire:navigate>
                                    {{ __('Editar') }}
                                </a>
                            </li>
                            <li>
                                <button
                                        wire:click="deleteAddress({{ $address->id }})"
                                        wire:confirm="{{ __('¿Estás seguro de que quieres eliminar esta dirección?') }}"
                                        class="text-primary"
                                >
                                    {{ __('Eliminar') }}
                                </button>
                            </li>
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-numaxlab-atomic::organisms.tier>
</div>