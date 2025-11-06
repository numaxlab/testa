@php
    if ($attributes->get('href')) {
        $openInnerWrapperTag = '<a href="'.$attributes->get('href').'" class="banner-inner-wrapper">';
        $closeInnerWrapperTag = '</a>';
    } else {
        $openInnerWrapperTag = '<div class="banner-inner-wrapper">';
        $closeInnerWrapperTag = '</div>';
    }
@endphp

<article {{ $attributes->merge(['class' => 'ml-banner'])->filter(fn ($value, $key) => ! in_array($key, ['href', 'image-src'])) }}>
    {!! $openInnerWrapperTag !!}
    @if ($attributes->get('image-src'))
        <div class="banner-background-overlay"
             style="background-image: url('{{ $attributes->get('image-src') }}');"></div>
    @endif
    <div class="banner-content">
        <h2 class="at-title">{{ $slot }}</h2>
        @if (!empty($content))
            <div class="banner-text">
                {{ $content }}
            </div>
        @endif
    </div>
    {!! $closeInnerWrapperTag !!}
</article>