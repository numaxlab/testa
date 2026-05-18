<div class="w-full h-full bg-cover bg-center bg-no-repeat py-16 {{ $slide->style === 'positive' ? 'text-black' : 'text-white' }}"
     style="background-image: url('{{ $slide->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}');"
>
    <div class="container mx-auto px-4">
        @if ($i === 0)
            <h1 class="at-heading is-1">{{ $slide->name }}</h1>
        @else
            <h2 class="at-heading is-1">{{ $slide->name }}</h2>
        @endif

        @if ($slide->description)
            <div class="mt-4 md:max-w-[75%] lg:max-w-[60%]">
                <div class="{{ $slide->style === 'positive' ? 'prose' : 'prose-invert' }}">
                    {!! $slide->description !!}
                </div>
            </div>
        @endif

        @if ($slide->button_text && $slide->link)
            <a href="{{ $slide->link }}"
               class="at-button {{ $slide->style === 'positive' ? 'is-primary' : 'bg-accent border-accent text-white' }} inline-block mt-5">
                {{ $slide->button_text }}
            </a>
        @endif
    </div>
</div>
