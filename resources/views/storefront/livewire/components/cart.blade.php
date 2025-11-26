<div x-data="{linesVisible: @entangle('linesVisible').live}">
    <button x-on:click="linesVisible = !linesVisible" class="text-primary relative">
        <i class="icon icon-shopping-bag" aria-hidden="true"></i>
        @if ($lines && count($lines) > 0)
            <span class="absolute bottom-0 end-0 inline-flex items-center justify-center w-4 h-4 text-xs font-medium text-white bg-primary rounded-full">
                {{ count($lines) }}
            </span>
        @endif
        <span class="sr-only">{{ __('Abrir la cesta de compra') }}</span>
    </button>

    <div
            class="fixed top-0 end-0 z-50 w-3/4 px-5 py-5 bg-white border border-primary lg:absolute lg:w-100"
            x-show="linesVisible"
            x-on:click.away="linesVisible = false"
            x-transition
            x-cloak>

        <h2 class="at-heading is-3">{{ __('Tu cesta') }}</h2>

        <button class="absolute text-primary transition-transform top-3 right-3 hover:scale-110"
                type="button"
                aria-label="{{ __('Cerrar cesta de compra') }}"
                x-on:click="linesVisible = false">
            <i class="icon icon-close" aria-hidden="true"></i>
        </button>

        <div class="mt-9">
            @if ($this->cart && count($lines) > 0)
                <ul class="divide-y divide-black space-y-4">
                    @foreach ($lines as $index => $line)
                        <li wire:key="line_{{ $line['id'] }}">
                            <article class="flex gap-4 pb-4">
                                <a
                                        href="{{ route('trafikrak.storefront.bookshop.products.show', $line['slug']) }}"
                                        wire:navigate class="block w-1/4"
                                >
                                    <img
                                            src="{{ $line['thumbnail'] }}"
                                            loading="lazy"
                                            alt=""/>
                                </a>

                                <div class="w-3/4">
                                    <h3 class="at-heading is-4 mb-2 line-clamp-1">
                                        <a
                                                href="{{ route('trafikrak.storefront.bookshop.products.show', $line['slug']) }}"
                                                wire:navigate
                                        >
                                            {{ $line['description'] }}
                                        </a>
                                    </h3>

                                    <div class="w-1/3">
                                        <x-numaxlab-atomic::atoms.forms.input
                                                wire:model.live="lines.{{ $index }}.quantity"
                                                wire:change="updateLines"
                                                type="number"
                                                class="text-xs mb-2"
                                        />
                                    </div>

                                    <p class="at-small">
                                        {{ $line['unit_price'] }}
                                    </p>

                                    <button
                                            class="at-small text-primary"
                                            type="button"
                                            wire:click="removeLine('{{ $line['id'] }}')">
                                        {{ __('Eliminar') }}
                                    </button>
                                </div>
                            </article>

                            @if ($errors->get('lines.' . $index . '.quantity'))
                                <div
                                        class="p-2 mb-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
                                        role="alert">
                                    @foreach ($errors->get('lines.' . $index . '.quantity') as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <ul class="flex flex-col divide-y divide-black border-t border-b border-black">
                    <li class="at-small py-2">
                        <i class="icon icon-shopping-bag" aria-hidden="true"></i>
                        {{ __('Subtotal pedido') }}: {{ $this->cart->total->formatted() }}
                    </li>
                </ul>
            @else
                <p class="py-4 text-sm">
                    {{ __('Tu cesta está vacía') }}
                </p>
            @endif
        </div>

        @if ($this->cart && count($lines) > 0)
            <a class="at-button is-primary mt-4" href="{{ route('trafikrak.storefront.checkout.summary') }}"
               wire:navigate>
                {{ __('Tramitar pedido') }}
            </a>
        @endif
    </div>
</div>
