<article class="container mx-auto px-4">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.media.homepage') }}">
                    {{ __('Mediateca') }}
                </a>
            </li>
            <li>
                {{ __('Audios') }}
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1 mt-4">{{ $audio->name }}</h1>
    </header>

    <div class="mx-auto lg:max-w-4xl">
        <div class="[&>iframe]:aspect-video [&>iframe]:w-full! [&>iframe]:h-auto! [&>div]:hidden">
            {!! $audio->source_id !!}
        </div>

        @if ($audio->description)
            <div class="prose max-w-full mt-6">
                {!! $audio->description !!}
            </div>
        @endif

        @include('trafikrak::storefront.partials.media.attachables', ['media' => $audio])
    </div>
</article>