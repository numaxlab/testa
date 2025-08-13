<?php

namespace Trafikrak\Storefront\GlobalSearch\Mappers;

use Trafikrak\Storefront\GlobalSearch\SearchResult;

class ProductMapper extends AbstractMapper
{
    public function map(): SearchResult
    {
        $title = $this->model->translateAttribute('name');

        if ($this->model->translateAttribute('subtitle')) {
            $title .= ' ' . $this->model->translateAttribute('subtitle');
        }

        if ($this->model->authors->isNotEmpty()) {
            $title .= ' | ' . $this->model->authors->pluck('name')->implode(', ');
        }

        return new SearchResult(
            'product',
            $this->model->id,
            $title,
            route('trafikrak.storefront.bookshop.products.show', $this->model->defaultUrl->slug),
            $this->score,
        );
    }
}
