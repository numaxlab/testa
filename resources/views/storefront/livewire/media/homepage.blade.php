<article>
    <header class="container mx-auto px-4">
        <h1 class="at-heading is-1 mb-10">{{ __('Mediateca') }}</h1>

        @include('trafikrak::storefront.partials.media.search-form')
    </header>

    @foreach ($tiers as $tier)
        <livewire:dynamic-component
                :component="'trafikrak.storefront.livewire.components.tier.' . $tier->livewire_component"
                :lazy="!$loop->first"
                :tier="$tier"
                :key="$tier->id"
        />
    @endforeach

    <div class="container mx-auto px-4">
        <livewire:trafikrak.storefront.livewire.components.media.latest-documents lazy/>
    </div>
</article>