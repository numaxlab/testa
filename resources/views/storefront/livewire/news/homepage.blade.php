<article class="container mx-auto px-4">
    <header class="mb-10">
        <h1 class="at-heading is-1">
            {{ __('Actualidad') }}
        </h1>
    </header>

    @if ($activities->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Actividades') }}
                </h2>

                <a href="{{ route('trafikrak.storefront.activities.index') }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ __('ver más') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 md:grid-cols-4">
                @foreach ($activities as $activity)
                    <li>
                        @if ($activity instanceof \Trafikrak\Models\News\Event)
                            <x-trafikrak::events.summary :event="$activity"/>
                        @elseif ($activity instanceof \Trafikrak\Models\Education\CourseModule)
                            <x-trafikrak::course-modules.activity :module="$activity"/>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    @endif

    @if ($articles->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Noticias') }}
                </h2>

                <a href="{{ route('trafikrak.storefront.articles.index') }}"
                   wire:navigate
                   class="at-small"
                >
                    {{ __('ver más') }}
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 md:grid-cols-4">
                @foreach ($articles as $article)
                    <li>
                        <x-trafikrak::articles.summary :article="$article"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</article>