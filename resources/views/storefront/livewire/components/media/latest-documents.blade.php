<div>
    @if ($documents->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Últimos documentos') }}
                </h2>

                <a class="at-small" href="{{ route('testa.storefront.media.documents.index') }}">
                    {{ __('Ver más') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($documents as $document)
                    <li>
                        <x-testa::documents.summary :media="$document" :href="route('testa.storefront.media.documents.download', $document)"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</div>