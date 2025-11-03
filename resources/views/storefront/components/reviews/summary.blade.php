<figure class="at-blockquote has-icon">
    <i class="blockquote-icon fa-solid fa-quote-left fa-2x text-primary" aria-hidden="false"></i>
    <blockquote>
        {{ $review->quote }}
    </blockquote>
    <figcaption class="blockquote-footer">
        @if ($review->author)
            <p class="font-bold">{{ $review->author }}</p>
        @endif
        @if ($review->media_name && $review->link)
            <a href="{{ $review->link }}" target="_blank">
                {{ $review->media_name }}
            </a>
        @elseif ($review->media_name)
            <p>{{ $review->media_name }}</p>
        @endif
    </figcaption>
</figure>