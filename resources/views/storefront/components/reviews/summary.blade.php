<figure class="at-blockquote has-icon">
    <i class="icon icon-hyphens fa-2x text-primary" aria-hidden="false"></i>
    <blockquote>
        {{ $review->quote }}
    </blockquote>
    <figcaption class="blockquote-footer mt-2">
        @if ($review->author)
            <p class="font-bold mb-0">{{ $review->author }}</p>
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