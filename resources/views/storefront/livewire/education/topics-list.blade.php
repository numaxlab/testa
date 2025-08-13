<div class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.education.homepage') }}">
                    {{ __('Formación') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">{{ __('Temas') }}</h1>
    </header>

    <ul class="grid gap-4 md:grid-cols-2 mt-10">
        @foreach ($topics as $topic)
            <li>
                <x-trafikrak::education-topics.summary :topic="$topic"/>
            </li>
        @endforeach
    </ul>
</div>