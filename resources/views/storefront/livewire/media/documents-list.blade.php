<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('testa.storefront.media.homepage') }}">
                    {{ __('Mediateca') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Documentos') }}
        </h1>
    </header>

    @if ($documents->isNotEmpty())
        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($documents as $document)
                <li>
                    <x-testa::documents.summary :media="$document" :href="Storage::url($document->path)"/>
                </li>
            @endforeach
        </ul>

        {{ $documents->links() }}
    @else
        <p>{{ __('No hay resultados.') }}</p>
    @endif
</article>
