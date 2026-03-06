<div class="relative min-h-100 w-full bg-cover bg-center bg-no-repeat py-16 {{ $slide->style === 'positive' ? 'text-black' : 'text-white' }}"
     style="background-image: url('{{ $slide->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}');"
>
    <div class="container mx-auto px-4">
        <h1 class="at-heading is-1">{{ $slide->name }}</h1>

        @if ($slide->description)
            <div class="mt-4 md:max-w-[75%] lg:max-w-[60%] {{ $slide->style === 'positive' ? 'prose' : 'prose-invert' }}">
                {!! $slide->description !!}
            </div>
        @endif

        @if ($slide->button_text && $slide->link)
            <a href="{{ $slide->link }}" class="at-button is-primary inline-block mt-5">
                {{ $slide->button_text }}
            </a>
        @endif
    </div>
</div>