<x-slot name="description">{{ Str::limit(strip_tags($video->description), 160) }}</x-slot>

<article class="container mx-auto px-4">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('testa.storefront.media.homepage') }}">
                    {{ __('Mediateca') }}
                </a>
            </li>
            <li>
                {{ __('Videos') }}
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1 mt-4">{{ $video->name }}</h1>
    </header>

    <div class="mx-auto lg:max-w-4xl">
        <div class="[&>iframe]:aspect-video [&>iframe]:w-full! [&>iframe]:h-auto! [&>div]:hidden">
            {!! $video->source_id !!}
        </div>

        @if ($video->description)
            <div class="prose max-w-full mt-6">
                {!! $video->description !!}
            </div>
        @endif

        @include('testa::storefront.partials.media.attachables', ['media' => $video])
    </div>
</article>