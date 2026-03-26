<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\News\Article;

final class GetLatestArticles
{
    public function execute(int $limit = 4): Collection
    {
        return Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->with(['defaultUrl'])
            ->take($limit)
            ->get();
    }
}
