<x-slot name="description">{{ Str::limit(strip_tags($topic->description), 160) }}</x-slot>


<article>
    <div class="container mx-auto px-4">
        <header class="md:flex gap-6 mb-10">
            <div class="md:w-1/2 lg:pr-20">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('testa.storefront.education.homepage') }}">
                            {{ __('Formación') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('testa.storefront.education.topics.index') }}">
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

            @if ($media)
                <figure class="mt-5 md:w-1/2">
                    {{ $media('large') }}
                </figure>
            @endif
        </header>

        <ul class="grid gap-6 md:grid-cols-3">
            @foreach ($courses as $course)
                <li>
                    <x-testa::courses.summary :course="$course"/>
                </li>
            @endforeach
        </ul>
    </div>
</article>