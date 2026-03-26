<?php

namespace Testa\Storefront\Queries\Media;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Video;
use Testa\Models\Media\Visibility;

final class GetMediaSearch
{
    private array $columns = ['id', 'name', 'description', 'source', 'source_id', 'created_at'];

    public function execute(string $q = '', int $perPage = 12): LengthAwarePaginator
    {
        $videosQuery = Video::query()
            ->select([...$this->columns, DB::raw("'videos' as type")])
            ->where('is_published', true)
            ->where('visibility', Visibility::PUBLIC->value)
            ->when($q, function ($query) use ($q) {
                $videosByQuery = Video::search($q)->take(PHP_INT_MAX)->get();
                $query->whereIn('id', $videosByQuery->pluck('id'));
            });

        $audiosQuery = Audio::query()
            ->select([...$this->columns, DB::raw("'audios' as type")])
            ->where('is_published', true)
            ->where('visibility', Visibility::PUBLIC->value)
            ->when($q, function ($query) use ($q) {
                $audiosByQuery = Audio::search($q)->take(PHP_INT_MAX)->get();
                $query->whereIn('id', $audiosByQuery->pluck('id'));
            });

        return $videosQuery
            ->union($audiosQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
