<?php

namespace Trafikrak\Storefront\GlobalSearch\Mappers;

use Trafikrak\Storefront\GlobalSearch\SearchResult;

class CourseMapper extends AbstractMapper
{
    public function map(): SearchResult
    {
        return new SearchResult(
            $this->model->searchableAs(),
            $this->model->id,
            $this->model->name,
            route('trafikrak.storefront.education.courses.show', $this->model->defaultUrl->slug),
            $this->score,
        );
    }
}
