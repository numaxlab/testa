<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Education\Course;
use Testa\Models\Education\Topic;

final class GetTopicCourses
{
    public function execute(Topic $topic, int $limit = 6): Collection
    {
        return Course::where('is_published', true)
            ->where('topic_id', $topic->id)
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
