<article>
    <div class="container mx-auto px-4">
        <header class="md:flex gap-6">
            <div class="md:w-1/2 lg:pr-20">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('trafikrak.storefront.education.homepage') }}">
                            {{ __('Formación') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trafikrak.storefront.education.topics.index') }}">
                            {{ __('Temas') }}
                        </a>
                    </li>
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1">{{ $topic->name }}</h1>

                @if ($topic->description)
                    <div class="mt-5">
                        {!! $topic->description !!}
                    </div>
                @endif
            </div>

            @if ($topic->media->isNotEmpty())
                <figure class="mt-5 md:w-1/2">
                    <img src="{{ $topic->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">
                </figure>
            @endif
        </header>

        <x-numaxlab-atomic::organisms.tier class="mt-9">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Cursos') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 md:grid-cols-3">
                @foreach ($courses as $course)
                    <li>
                        <x-trafikrak::courses.summary :course="$course"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>
</article>