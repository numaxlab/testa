<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;

final class GetModuleAttachments
{
    public function execute(CourseModule $module): Collection
    {
        return Attachment::where('attachable_type', (new CourseModule)->getMorphClass())
            ->where('attachable_id', $module->id)
            ->whereHas('media', fn($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }
}
