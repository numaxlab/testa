<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Models\Education\Course;
use Testa\Models\Education\Topic;
use Testa\Models\EventDeliveryMethod;

class CoursesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $t = '';

    #[Url]
    public string $dm = '';

    public function render(): View
    {
        $deliveryMethods = [
            EventDeliveryMethod::IN_PERSON->value => __(
                'testa::coursemodule.form.delivery_method.options.in_person',
            ),
            EventDeliveryMethod::ONLINE->value => __(
                'testa::coursemodule.form.delivery_method.options.online',
            ),
            EventDeliveryMethod::HYBRID->value => __(
                'testa::coursemodule.form.delivery_method.options.hybrid',
            ),
            EventDeliveryMethod::MOOC->value => __(
                'testa::coursemodule.form.delivery_method.options.mooc',
            ),
        ];

        $topics = Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();

        $queryBuilder = Course::where('is_published', true)
            ->with([
                'horizontalImage',
                'verticalImage',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc')
            ->when($this->dm, function ($query) {
                $query->where('delivery_method', $this->dm);
            });

        if ($this->q) {
            $coursesByQuery = Course::search($this->q)->take(PHP_INT_MAX)->get();

            $queryBuilder->whereIn('id', $coursesByQuery->pluck('id'));
        }

        if ($this->t) {
            $queryBuilder->whereHas('topic', function ($query) {
                $query->where('id', $this->t);
            });
        }

        $courses = $queryBuilder->paginate(12);

        return view(
            'testa::storefront.livewire.education.courses-list',
            compact('topics', 'deliveryMethods', 'courses'),
        )->title(__('Cursos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
