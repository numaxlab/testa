<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\Topic;

class TopicPage extends Page
{
    public Topic $topic;

    public Collection $courses;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Topic)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.media',
            ],
        );

        $this->topic = $this->url->element;

        $this->courses = Course::where('is_published', true)
            ->where('topic_id', $this->topic->id)
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        $media = $this->topic->getFirstMedia(config('lunar.media.collection'));

        return view('trafikrak::storefront.livewire.education.topic', compact('media'))
            ->title($this->topic->name);
    }
}
