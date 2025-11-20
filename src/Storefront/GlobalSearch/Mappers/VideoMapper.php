<?php

namespace Trafikrak\Storefront\GlobalSearch\Mappers;

use Trafikrak\Storefront\GlobalSearch\SearchResult;

class VideoMapper extends AbstractMapper
{
    public function map(): SearchResult
    {
        return new SearchResult(
            $this->model->searchableAs(),
            $this->model->id,
            $this->model->name,
            route('trafikrak.storefront.media.search', ['q' => $this->model->name]),
            $this->score,
        );
    }
}
