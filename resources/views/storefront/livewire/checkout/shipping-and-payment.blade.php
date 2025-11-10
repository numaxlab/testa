<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.checkout.summary') }}">
                    {{ __('Volver al carrito') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('¿Cómo quieres recibir tu pedido?') }}
        </h1>
    </header>

    <div class="lg:flex lg:gap-6">
        <div class="lg:w-1/2">
            @include('trafikrak::storefront.partials.checkout.address', [
                'type' => 'shipping',
                'step' => $steps['shipping_address'],
            ])
        </div>
        <div class="lg:w-1/2">
            @include('trafikrak::storefront.partials.checkout.shipping-options', [
                'step' => $steps['shipping_option'],
            ])
        </div>
    </div>

    <form method="post" action="{{ route('trafikrak.storefront.checkout.process-payment') }}">
        @csrf

        <div class="lg:flex lg:gap-6">
            <div class="lg:w-1/2">
                @include('trafikrak::storefront.partials.checkout.address', [
                   'type' => 'billing',
                   'step' => $steps['billing_address'],
               ])
            </div>
            <div class="lg:w-1/2">
                @include('trafikrak::storefront.partials.checkout.payment', [
                'step' => $steps['payment'],
            ])
            </div>
        </div>

        <div class="flow-root my-7">
            <h2 class="at-heading is-4">
                {{ __('Resumen del pedido') }}
            </h2>
            <dl class="mt-4 text-sm divide-y divide-black">
                <div class="flex flex-wrap py-2">
                    <dt class="w-1/2 font-medium">
                        {{ __('Subtotal') }}
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $cart->subTotal->formatted() }}
                    </dd>
                </div>

                @if ($this->shippingOption)
                    <div class="flex flex-wrap py-2">
                        <dt class="w-1/2 font-medium">
                            {{ __('Gastos de envío') }}
                        </dt>

                        <dd class="w-1/2 text-right">
                            {{ $this->shippingOption->getPrice()->formatted() }}
                        </dd>
                    </div>
                @endif

                @foreach ($cart->taxBreakdown->amounts as $tax)
                    <div class="flex flex-wrap py-2" wire:key="{{ 'cart-tax-'.$tax->identifier }}">
                        <dt class="w-1/2 font-medium">
                            {{ $tax->description }}
                        </dt>

                        <dd class="w-1/2 text-right">
                            {{ $tax->price->formatted() }}
                        </dd>
                    </div>
                @endforeach

                <div class="flex flex-wrap py-2">
                    <dt class="w-1/2 font-medium">
                        {{ __('Total') }}
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $cart->total->formatted() }}
                    </dd>
                </div>
            </dl>
        </div>

        <x-numaxlab-atomic::atoms.button type="submit" class="is-primary w-full">
            {{ __('Pagar') }}
        </x-numaxlab-atomic::atoms.button>
    </form>
</article>