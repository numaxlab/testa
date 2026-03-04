<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <h1 class="at-heading is-1">{{ __('Pedido finalizado') }}</h1>

    <div class="grid gap-15 md:grid-cols-2 mt-10">
        <div class="at-lead">
            <p>{{ __('Muchas gracias por tu pedido :reference. En breves momentos recibirás un correo electrónico con todos los datos. También puedes revisarlo en tu cuenta. Si tienes cualquier duda, puedes contactar con nosotros en info@libreria.com', ['reference' => $order->reference]) }}</p>
        </div>
        <div class="border-t border-primary">
            <div class="border-b border-black py-2">
                <i class="icon icon-calendar mr-2" aria-hidden="true"></i>
                {{ $order->placed_at?->format('d/m/Y') ?? $order->created_at->format('d/m/Y') }}
            </div>

            @if($order->shipping_breakdown->items->isNotEmpty())
                <div class="border-b border-black py-2">
                    <i class="icon icon-shipping mr-2" aria-hidden="true"></i>
                    {{ $order->shipping_breakdown->items->pluck('name')->implode(', ') }}
                    @if($shippingDescription)
                        <div class="block text-sm">{!! $shippingDescription !!}</div>
                    @endif
                </div>
            @endif

            @if(filled($order->meta['Método de pago'] ?? null))
                <div class="border-b border-black py-2">
                    <i class="icon icon-shopping-bag mr-2" aria-hidden="true"></i>
                    {{ $order->meta['Método de pago'] }}
                </div>
            @endif

            @if(($order->meta['Es un regalo'] ?? null) === __('Sí'))
                <div class="border-b border-black py-2">
                    <i class="icon icon-heart mr-2" aria-hidden="true"></i>
                    {{ __('Es un regalo') }}
                </div>
            @endif

            <div class="border-b border-black py-2 flex justify-between">
                <span>{{ __('Subtotal') }}</span>
                <span>{{ $order->sub_total->formatted() }}</span>
            </div>

            @if($order->discount_total->value > 0)
                <div class="border-b border-black py-2 flex justify-between">
                    <span>{{ __('Descuento') }}</span>
                    <span>-{{ $order->discount_total->formatted() }}</span>
                </div>
            @endif

            <div class="border-b border-black py-2 flex justify-between">
                <span>{{ __('Envío') }}</span>
                <span>{{ $order->shipping_total->formatted() }}</span>
            </div>

            <div class="border-b border-black py-2 flex justify-between">
                <span>{{ __('Impuestos') }}</span>
                <span>{{ $order->tax_total->formatted() }}</span>
            </div>

            <div class="border-b border-black py-2 flex justify-between font-bold">
                <span>{{ __('Total') }}</span>
                <span>{{ $order->total->formatted() }}</span>
            </div>
        </div>
    </div>

    <ul class="flex gap-10 mt-10">
        <li>
            <a href="{{ route('orders.show', $order->reference) }}" wire:navigate class="at-button is-primary">
                {{ __('Ver pedido en mi cuenta') }}
            </a>
        </li>
        <li>
            <a href="{{ route('testa.storefront.homepage') }}" wire:navigate class="at-button is-primary">
                {{ __('Ir a la portada') }}
            </a>
        </li>
    </ul>
</article>
