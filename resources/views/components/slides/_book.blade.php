<div class="w-full h-full flex items-center {{ $slide->style === 'positive' ? 'bg-white text-black border-t border-b border-black' : 'bg-black text-white' }}">
    <div class="container mx-auto px-4">
        <div class="flex items-center gap-8 lg:gap-16">
            @if ($slide->hasMedia(config('lunar.media.collection')))
                <div class="shrink-0" style="width: clamp(6rem, 16vw, 13rem)">
                    <img src="{{ $slide->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                         alt="{{ $slide->name }}"
                         class="w-full h-auto object-contain"
                         style="box-shadow: 0.5rem 0.5rem 1.5rem rgba(0,0,0,0.35)">
                </div>
            @endif

            <div style="min-width: 0">
                @if ($i === 0)
                    <h1 class="at-heading is-2 mb-2">{{ $slide->name }}</h1>
                @else
                    <h2 class="at-heading is-2 mb-2">{{ $slide->name }}</h2>
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
    </div>
</div>
