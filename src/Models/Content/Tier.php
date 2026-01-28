<?php

namespace Testa\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Collection;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Content\TierFactory;
use Testa\Models\Attachment;
use Testa\Models\Education\Course;
use Testa\Models\Education\Topic;

class Tier extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'link',
        'link_name',
    ];
    protected $guarded = [];

    protected static function newFactory()
    {
        return TierFactory::new();
    }

    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(Banner::class);
    }

    public function collections(): BelongsToMany
    {
        $prefix = config('lunar.database.table_prefix');

        return $this->belongsToMany(Collection::modelClass(), "{$prefix}collection_tier");
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function educationTopics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'education_topic_tier');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getHasLinkAttribute(): bool
    {
        return $this->link && $this->link_name;
    }

    public function getLivewireComponentAttribute(): ?string
    {
        return match ($this->type) {
            TierType::RELATED_CONTENT_BANNER => 'banner',
            TierType::RELATED_CONTENT_COLLECTION => 'collection',
            TierType::RELATED_CONTENT_COURSE => 'courses',
            TierType::RELATED_CONTENT_EDUCATION_TOPIC => 'education-topics',
            TierType::RELATED_CONTENT_MEDIA => 'media',
            TierType::EDITORIAL_LATEST => 'editorial-latest',
            TierType::EDUCATION_UPCOMING => 'education-upcoming',
            TierType::EVENTS_UPCOMING => 'events-upcoming',
            TierType::ARTICLES_LATEST => 'articles-latest',
            default => null,
        };
    }

    protected function casts(): array
    {
        return [
            'section' => Section::class,
            'type' => TierType::class,
        ];
    }
}
