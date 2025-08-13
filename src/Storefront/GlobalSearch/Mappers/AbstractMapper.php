<?php

namespace Trafikrak\Storefront\GlobalSearch\Mappers;

use Illuminate\Database\Eloquent\Model;
use Trafikrak\Storefront\GlobalSearch\SearchResult;

abstract class AbstractMapper
{
    public function __construct(protected readonly Model $model, protected readonly float $score) {}

    abstract public function map(): SearchResult;
}