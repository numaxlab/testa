@if (Auth::check())
    <p class="text-lg">
        {{ __('Hola') }}, <strong>{{ Auth::user()->latestCustomer()?->first_name }}</strong>
    </p>
@else
    @include('testa::storefront.partials.checkout.embed-auth')
@endif
