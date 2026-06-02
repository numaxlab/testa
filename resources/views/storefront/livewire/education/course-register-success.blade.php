<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <h1 class="at-heading is-1">{{ __('Inscripción completada') }}</h1>

    <div class="at-lead mt-10 lg:max-w-2/3">
        @if(filled($successText))
            {!! $successText !!}
        @endif
    </div>

    <ul class="flex gap-10 mt-10">
        <li>
            <a href="{{ route('dashboard') }}" wire:navigate class="at-button is-primary">
                {{ __('Ver todos mis cursos') }}
            </a>
        </li>
        <li>
            <a href="{{ route('testa.storefront.homepage') }}" wire:navigate class="at-button is-primary">
                {{ __('Ir a la portada') }}
            </a>
        </li>
    </ul>
</article>