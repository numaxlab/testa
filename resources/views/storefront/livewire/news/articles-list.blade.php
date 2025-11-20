<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.news.homepage') }}">
                    {{ __('Actualidad') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Noticias') }}
        </h1>

        <form class="my-6 flex flex-col gap-3 md:flex-row md:gap-6" wire:submit.prevent="search">
            <div class="relative w-full">
                <x-numaxlab-atomic::atoms.forms.input
                        type="search"
                        wire:model="q"
                        name="q"
                        id="query"
                        placeholder="{{ __('Buscar en noticias') }}"
                        aria-label="{{ __('Buscar en noticias') }}"
                        autocomplete="off"
                />
                <button type="submit" aria-label="{{ __('Buscar') }}" class="text-primary absolute inset-y-0 right-3">
                    <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </header>

    @if ($articles->isNotEmpty())
        <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($articles as $article)
                <li>
                    <x-trafikrak::articles.summary :article="$article"/>
                </li>
            @endforeach
        </ul>

        {{ $articles->links() }}
    @else
        <p>{{ __('No hay resultados.') }}</p>
    @endif
</article>