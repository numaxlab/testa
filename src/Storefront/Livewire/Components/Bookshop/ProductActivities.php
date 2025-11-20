<?php

namespace Trafikrak\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\News\Event;
use Trafikrak\Storefront\Livewire\News\ActivitiesListPage;

class ProductActivities extends Component
{
    public Product $product;

    public Collection $activities;

    private array $columns = ['id', 'name', 'subtitle', 'starts_at'];

    public function mount(): void
    {
        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->whereHas('products', fn ($query) => $query->where('product_id', $this->product->id));

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->whereHas('products', fn ($query) => $query->where('product_id', $this->product->id));

        $this->activities = ActivitiesListPage::eagerLoadResults(
            $eventsQuery
                ->union($courseModulesQuery)
                ->orderBy('starts_at', 'desc')
                ->get(),
        );
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.bookshop.product-activities');
    }
}
