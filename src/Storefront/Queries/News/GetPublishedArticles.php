<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Testa\Models\News\Article;

final class GetPublishedArticles
{
    public function execute(int $perPage = 12): LengthAwarePaginator
    {
        return Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->with(['defaultUrl', 'media'])
            ->paginate($perPage);
    }
}
