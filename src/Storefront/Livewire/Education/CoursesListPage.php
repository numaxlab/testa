<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Models\EventDeliveryMethod;
use Testa\Storefront\Queries\Education\GetCourses;
use Testa\Storefront\Queries\Education\GetPublishedTopics;

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

        $topics = new GetPublishedTopics()->execute();

        $courses = new GetCourses()->execute($this->q, $this->t, $this->dm);

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
