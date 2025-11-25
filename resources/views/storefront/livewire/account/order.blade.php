<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('dashboard') }}">
                    {{ __('Mi cuenta') }}
                </a>
            </li>
            <li>
                <a href="{{ route('orders.index') }}">
                    {{ __('Mis pedidos') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Pedido :reference', ['reference' => $order->reference]) }}
        </h1>
    </header>

    <div class="grid gap-15 md:grid-cols-2">
        <div class="border-t border-primary">
            <a wire:navigate class="block border-b border-primary py-2">
                <i class="icon icon-doc mr-2" aria-hidden="true"></i>
                {{ __('Descargar factura') }}
            </a>
            <div class="border-b border-black py-2">
                <i class="icon icon-calendar mr-2" aria-hidden="true"></i>
                {{ $order->created_at->format('d/m/Y') }}
            </div>
            <div class="border-b border-black py-2">
                <i class="icon icon-shipping mr-2" aria-hidden="true"></i>
                {{ $order->shipping_breakdown->items->pluck('name')->implode(', ') }}
                {{ $order->shipping_total->formatted() }}
            </div>
            <div class="border-b border-black py-2">
                <i class="icon icon-shopping-bag mr-2" aria-hidden="true"></i>
                {{ __('Total') }} {{ $order->total->formatted() }}
            </div>

            <a wire:navigate class="at-button is-primary mt-10">
                {{ __('Contacta con la tienda') }}
            </a>
        </div>

        <ul class="divide-y divide-black space-y-4">
            @foreach ($order->productLines as $line)
                <li class="pb-4">
                    <x-trafikrak::products.horizontal
                            :product="$line->purchasable->product"
                            :href="route('trafikrak.storefront.bookshop.products.show', $line->purchasable->product->defaultUrl->slug)"
                    >
                        <x-slot name="actions">
                            {{ $line->quantity }} {{ $line->quantity > 1 ? __('unidades') : __('unidad') }}<br>
                            {{ $line->total->formatted() }}
                        </x-slot>
                    </x-trafikrak::products.horizontal>
                </li>
            @endforeach
        </ul>
    </div>
</article>