<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Education\Topic;
use Testa\Storefront\Queries\Education\GetTopicCourses;

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

        $this->courses = new GetTopicCourses()->execute($this->topic);
    }

    public function render(): View
    {
        $media = $this->topic->getFirstMedia(config('lunar.media.collection'));

        return view('testa::storefront.livewire.education.topic', compact('media'))
            ->title($this->topic->name);
    }
}
