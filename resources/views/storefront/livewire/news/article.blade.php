<article class="container mx-auto px-4">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.news.homepage') }}" wire:navigate>
                    {{ __('Actualidad') }}
                </a>
            </li>
            <li>
                <a href="{{ route('trafikrak.storefront.articles.index') }}" wire:navigate>
                    {{ __('Noticias') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1 mt-4">{{ $article->name }}</h1>
    </header>

    <div class="mx-auto lg:max-w-4xl">
        <img src="{{ Storage::url($article->image) }}" alt="">

        <div class="prose max-w-full mt-10">
            {!! $article->content !!}
        </div>

        <livewire:trafikrak.storefront.livewire.components.news.article-products
                lazy
                :article="$article"
        />
    </div>
</article>