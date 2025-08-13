<?php

namespace Trafikrak\Storefront\GlobalSearch;

use Illuminate\Support\Collection;
use Lunar\Models\Product;
use Meilisearch\Client;
use Meilisearch\Contracts\MultiSearchFederation;
use Meilisearch\Contracts\SearchQuery;
use Trafikrak\Models\Education\Course;
use Trafikrak\Storefront\GlobalSearch\Mappers\CourseMapper;
use Trafikrak\Storefront\GlobalSearch\Mappers\ProductMapper;

class GlobalSearch
{
    public int $processingTimeMs = 0;
    public int $estimatedTotalHits = 0;
    private array $mappers = [
        Product::class => ProductMapper::class,
        Course::class => CourseMapper::class,
    ];
    private array $indexes = [];

    private ?string $contentType = null;

    public function __construct(private readonly Client $meilisearch)
    {
        foreach ($this->mappers as $model => $mapper) {
            $this->indexes[(new $model)->searchableAs()] = $model;
        }
    }

    public function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getResults(string $query): Collection
    {
        if ($this->contentType !== null && in_array($this->contentType, array_keys($this->indexes))) {
            $queries = [
                (new SearchQuery())
                    ->setIndexUid($this->contentType)
                    ->setQuery($query),
            ];
        } else {
            $queries = [
                (new SearchQuery())
                    ->setIndexUid((new Product)->searchableAs())
                    ->setQuery($query),
                (new SearchQuery())
                    ->setIndexUid((new Course)->searchableAs())
                    ->setQuery($query),
            ];
        }

        $results = $this->meilisearch->multiSearch(
            $queries,
            (new MultiSearchFederation())->setLimit(10),
        );

        $this->processingTimeMs = $results['processingTimeMs'];
        $this->estimatedTotalHits = $results['estimatedTotalHits'];

        return $this->mapResults($results);
    }

    private function mapResults(array $results): Collection
    {
        if (is_null($results) || count($results['hits']) === 0) {
            return collect();
        }

        $hits = collect($results['hits'])->keyBy('id');

        foreach ($hits->groupBy('_federation.indexUid') as $indexUid => $groupedResults) {
            $model = $this->indexes[$indexUid];
            $modelInstance = new $model;

            $objectIds = collect($groupedResults)->pluck($modelInstance->getScoutKeyName())->values()->all();

            $objects = $model::whereIn($modelInstance->getScoutKeyName(), $objectIds)
                ->with(['defaultUrl'])
                ->get();

            $objects->each(function ($object) use ($hits) {
                $hit = $hits
                    ->where('id', $object->getScoutKey())
                    ->where('_federation.indexUid', $object->searchableAs())
                    ->first();

                $hit['model'] = $object;

                $hits->put($object->getScoutKey(), $hit);
            });
        }

        $mappedResults = collect();

        $hits->each(function ($hit) use ($mappedResults) {
            $model = $this->indexes[$hit['_federation']['indexUid']];
            $mapper = new $this->mappers[$model]($hit['model'], $hit['_federation']['weightedRankingScore']);

            $mappedResults->push($mapper->map());
        });

        return $mappedResults;
    }
}
