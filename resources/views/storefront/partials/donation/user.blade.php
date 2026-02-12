@if (Auth::check())
    <p class="text-lg">
        {{ __('Hola') }}, <strong>{{ Auth::user()->latestCustomer()?->first_name }}</strong>
    </p>
@else
    @include('testa::storefront.partials.checkout.embed-auth')
@endif

<div class="mt-5 grid gap-4">
    <x-numaxlab-atomic::atoms.input
            wire:model="id_number"
            type="text"
            name="id_number"
            id="id_number"
            maxlength="20"
    >
        {{ __('DNI/NIF') }}
    </x-numaxlab-atomic::atoms.input>

    <x-numaxlab-atomic::atoms.textarea
            wire:model="comments"
            name="comments"
            id="comments"
            maxlength="500"
            rows="3"
    >
        {{ __('Comentarios') }}
    </x-numaxlab-atomic::atoms.textarea>
</div>
