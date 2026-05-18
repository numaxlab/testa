<div class="w-full h-full {{ $slide->style === 'positive' ? 'bg-white text-black border-t border-b border-black' : 'bg-black text-white' }}">
    <div class="h-full grid lg:grid-cols-[5fr_7fr]">
        <div class="flex items-center px-6 py-8 lg:px-8 lg:pr-16">
            <div>
                @if ($i === 0)
                    <h1 class="at-heading is-2 mb-2">{{ $slide->name }}</h1>
                @else
                    <h2 class="at-heading is-2 mb-2">{{ $slide->name }}</h2>
                @endif

                @if ($slide->description)
                    <div class="{{ $slide->style === 'positive' ? 'prose' : 'prose-invert' }}">
                        {!! $slide->description !!}
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

        @if ($slide->hasMedia(config('lunar.media.collection')))
            <div class="hidden lg:block overflow-hidden">
                <img src="{{ $slide->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                     alt="" class="w-full h-full object-cover">
            </div>
        @endif
    </div>
</div>
