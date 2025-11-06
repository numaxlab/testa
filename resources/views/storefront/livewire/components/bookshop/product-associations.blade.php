<div>
    @if ($associations->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Relacionados') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="overflow-x-auto">
            <ul class="grid grid-flow-col auto-cols-[40%] md:auto-cols-[25%] gap-6">
                @foreach ($associations as $association)
                    <li>
                        <x-trafikrak::products.summary
                                :product="$association->target"
                                :href="route('trafikrak.storefront.bookshop.products.show', $association->target->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>
            </div>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</div>