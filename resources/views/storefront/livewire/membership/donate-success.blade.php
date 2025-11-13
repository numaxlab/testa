<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <h1 class="at-heading is-1">{{ __('Gracias por tu donación') }}</h1>

    <div class="at-lead mt-10 lg:max-w-2/3">
        <p>Muchas gracias por tu donación. En breves momentos recibirás un correo electrónico con todos los datos. Si
            tienes cualquier duda, puedes contactar con nosotros.</p>
    </div>

    <ul class="flex gap-10 mt-10">
        <li>
            <a href="{{ route('dashboard') }}" wire:navigate class="at-button is-primary">
                {{ __('Ver mi cuenta') }}
            </a>
        </li>
        <li>
            <a href="{{ route('trafikrak.storefront.homepage') }}" wire:navigate class="at-button is-primary">
                {{ __('Ir a la portada') }}
            </a>
        </li>
    </ul>
</article>