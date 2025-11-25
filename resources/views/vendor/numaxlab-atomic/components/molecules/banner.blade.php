@php
    if ($attributes->get('href')) {
        $openInnerWrapperTag = '<a href="'.$attributes->get('href').'" class="banner-inner-wrapper">';
        $closeInnerWrapperTag = '</a>';
    } else {
        $openInnerWrapperTag = '<div class="banner-inner-wrapper">';
        $closeInnerWrapperTag = '</div>';
    }
@endphp

<article {{ $attributes->merge(['class' => 'ml-banner min-h-100 bg-primary'])->filter(fn ($value, $key) => ! in_array($key, ['href', 'image-src'])) }}>
    {!! $openInnerWrapperTag !!}
    @if ($attributes->get('image-src'))
        <div class="banner-background-overlay grayscale after:absolute after:inset-0 after:z-10 after:bg-linear-to-t after:from-black after:to-transparent"
             style="background-image: url('{{ $attributes->get('image-src') }}');"></div>
    @endif
    <div class="banner-content flex flex-col gap-2 justify-end text-white z-20">
        <h2 class="at-heading is-3">{{ $slot }}</h2>

        @if (!empty($content))
            <div class="banner-text max-w-[65%]">
                {{ $content }}
            </div>
        @endif
    </div>
    @if ($attributes->get('image-src'))
        <div class="absolute inset-0 bg-primary mix-blend-multiply z-10"></div>
    @endif
    {!! $closeInnerWrapperTag !!}
</article>