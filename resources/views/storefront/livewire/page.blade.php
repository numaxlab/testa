<article class="container mx-auto px-4">
    <header class="mb-10">
        @if ($page->has_breadcrumb)
            <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                <li>
                    <a href="{{ route($page->breadcrumb_route_name) }}" wire:navigate>
                        {{ $page->human_section }}
                    </a>
                </li>
            </x-numaxlab-atomic::molecules.breadcrumb>
        @endif

        <h1 class="at-heading is-1">{{ $page->name }}</h1>
    </header>

    <div class="lg:flex lg:gap-10">
        @if ($page->content)
            <nav class="border-t-2 border-neutral-400 py-4 lg:w-2/12">
                <ul>
                    @foreach ($page->content as $block)
                        <li class="mb-2">
                            <a href="#{{ Str::slug($block['name']) }}">
                                {{ $block['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif

        <div class="lg:w-8/12">
            @if ($page->intro)
                <div class="at-lead  mb-4">
                    {!! $page->intro !!}
                </div>
            @endif

            @if ($page->description)
                <div class="mb-10">
                    {!! $page->description !!}
                </div>
            @endif

            @if ($page->content)
                @foreach ($page->content as $block)
                    <article id="{{ Str::slug($block['name']) }}" class="mb-10">
                        <h2 class="at-heading is-2">
                            {{ $block['name'] }}
                        </h2>

                        @if ($block['description'])
                            <div class="mt-5">
                                {!! $block['description'] !!}
                            </div>
                        @endif
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</article>