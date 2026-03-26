<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Visibility;

final class GetPublicAttachmentsForModules
{
    public function execute(Collection $modules): Collection
    {
        return Attachment::where('attachable_type', (new CourseModule)->getMorphClass())
            ->whereIn('attachable_id', $modules->pluck('id'))
            ->whereHas(
                'media',
                fn($query) => $query->where('is_published', true)->where('visibility', Visibility::PUBLIC->value),
            )
            ->with('media')
            ->get();
    }
}
