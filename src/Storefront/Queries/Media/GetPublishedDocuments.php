<?php

namespace Testa\Storefront\Queries\Media;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Testa\Models\Media\Document;

final class GetPublishedDocuments
{
    public function execute(int $perPage = 16): LengthAwarePaginator
    {
        return Document::where('is_published', true)
            ->paginate($perPage);
    }
}
