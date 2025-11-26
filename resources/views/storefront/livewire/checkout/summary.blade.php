<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <h1 class="at-heading is-1">Carrito</h1>

    <ul class="border-y border-black divide-y divide-black mt-10">
        @foreach ($lines as $key => $line)
            <li>
                <x-trafikrak::products.in-cart
                        :href="route('trafikrak.storefront.bookshop.products.show', $line['slug'])"
                        :image="$line['thumbnail']"
                        :price="$line['unit_price']"
                        wire:key="line_{{ $line['id'] }}"
                >
                    {{ $line['description'] }}

                    <x-slot:quantity>
                        <x-numaxlab-atomic::atoms.forms.input
                                wire:model.live="lines.{{ $key }}.quantity"
                                wire:change="updateLines"
                                type="number"
                                class="text-xs"
                        />
                    </x-slot:quantity>

                    <x-slot:actions>
                        <button
                                class="at-small text-primary"
                                type="button"
                                wire:click="removeLine('{{ $line['id'] }}')">
                            Eliminar
                        </button>
                    </x-slot:actions>
                </x-trafikrak::products.in-cart>

                @if ($errors->get('lines.' . $key . '.quantity'))
                    <div
                            class="p-2 mb-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
                            role="alert">
                        @foreach ($errors->get('lines.' . $key . '.quantity') as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
            </li>
        @endforeach
    </ul>

    @if ($this->cart->subTotal)
        <div class="mt-10">
            <ul class="divide-y divide-black border-y border-black">
                <li class="at-small py-2">
                    <i class="icon icon-shopping-bag" aria-hidden="true"></i>
                    {{ __('Total pedido') }}: {{ $this->cart->total->formatted() }}
                </li>
            </ul>
        </div>

        <div class="flex gap-10 mt-10">
            <a
                    href="{{ route('trafikrak.storefront.bookshop.homepage') }}"
                    class="at-button is-secondary md:w-1/2"
                    wire:navigate
            >
                {{ __('Seguir comprando') }}
            </a>

            <a
                    class="at-button is-primary md:w-1/2"
                    href="{{ route('trafikrak.storefront.checkout.shipping-and-payment') }}"
                    wire:navigate
            >
                {{ __('Finalizar pedido') }}
            </a>
        </div>
    @endif
</article>