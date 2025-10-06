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

    @include('trafikrak::storefront.partials.checkout.address', [
        'type' => 'billing',
        'step' => $steps['billing_address'],
    ])

    @include('lunar-geslib::storefront.partials.checkout.payment', [
        'step' => $steps['payment'],
    ])

    <div class="flow-root mt-7">
        <dl class="-my-4 text-sm divide-y divide-gray-100">
            <div class="flex flex-wrap py-4">
                <dt class="w-1/2 font-medium">
                    Subtotal
                </dt>

                <dd class="w-1/2 text-right">
                    {{ $cart->subTotal->formatted() }}
                </dd>
            </div>

            @if ($this->shippingOption)
                <div class="flex flex-wrap py-4">
                    <dt class="w-1/2 font-medium">
                        Gastos de envío
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $this->shippingOption->getPrice()->formatted() }}
                    </dd>
                </div>
            @endif

            @foreach ($cart->taxBreakdown->amounts as $tax)
                <div class="flex flex-wrap py-4" wire:key="{{ 'cart-tax-'.$tax->identifier }}">
                    <dt class="w-1/2 font-medium">
                        {{ $tax->description }}
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $tax->price->formatted() }}
                    </dd>
                </div>
            @endforeach

            <div class="flex flex-wrap py-4">
                <dt class="w-1/2 font-medium">
                    Total
                </dt>

                <dd class="w-1/2 text-right">
                    {{ $cart->total->formatted() }}
                </dd>
            </div>
        </dl>
    </div>

    <form wire:submit="checkout" class="mt-7">
        <x-numaxlab-atomic::atoms.button type="submit" class="is-primary w-full">
            {{ __('Pagar') }}
        </x-numaxlab-atomic::atoms.button>
    </form>
</article>