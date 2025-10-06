<div>
    @if ($subscription)
        <article class="border-t pt-2">
            <h2 class="at-heading is-3">
                {{ __('Eres socia de :plan', ['plan' => $subscription->plan->full_name]) }}
            </h2>
            <p>{{ __('Hasta el :date', ['date' => $subscription->expires_at->format('d/m/Y')]) }}</p>
        </article>
    @else
        <article class="bg-secondary-light p-5">
            <h2 class="at-heading is-2 mb-2">
                {{ __('¿Quieres apoyar nuestro proyecto?') }}
            </h2>
            <p>Descripción apoyo</p>
            <a
                    href="{{ route('trafikrak.storefront.membership.homepage') }}"
                    wire:navigate
                    class="at-button is-primary mt-5"
            >
                {{ __('Asóciate o dona') }}
            </a>
        </article>
    @endif
</div>