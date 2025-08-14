<?php

namespace Trafikrak\Storefront\GlobalSearch;

use Livewire\Wireable;

readonly class SearchResult implements Wireable
{
    public string $icon;

    public function __construct(
        public string $type,
        public string $id,
        public string $title,
        public string $url,
        public float $score = 0,
    ) {
        $this->icon = match ($this->type) {
            'products' => 'fa-book-open',
            'courses' => 'fa-calendar-days',
            default => 'fa-magnifying-glass',
        };
    }

    public static function fromLivewire($value)
    {
        return new static(
            $value['type'],
            $value['id'],
            $value['title'],
            $value['url'],
            $value['score'] ?? 0,
        );
    }

    public function toLivewire()
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'score' => $this->score,
        ];
    }
}
