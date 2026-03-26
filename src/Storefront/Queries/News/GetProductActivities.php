<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Contracts\Product;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;
use Testa\Storefront\Livewire\News\ActivitiesListPage;

final class GetProductActivities
{
    private array $columns = ['id', 'starts_at'];

    public function execute(Product $product): Collection
    {
        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->whereHas('products', fn($query) => $query->where('product_id', $product->id));

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->whereHas('products', fn($query) => $query->where('product_id', $product->id));

        return ActivitiesListPage::eagerLoadResults(
            $eventsQuery
                ->union($courseModulesQuery)
                ->orderBy('starts_at', 'desc')
                ->get(),
        );
    }
}
