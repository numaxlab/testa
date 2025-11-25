<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Database\Factories\Education\TopicFactory;
use Trafikrak\Media\StandardMediaDefinitions;

class Topic extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasUrls;
    use HasMedia;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'subtitle',
        'description',
    ];
    protected $table = 'education_topics';
    protected $guarded = [];

    protected static function newFactory()
    {
        return TopicFactory::new();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    protected function getDefinitionClass()
    {
        $conversionClasses = config('lunar.media.definitions', []);

        return $conversionClasses['education-topic']
            ?? $conversionClasses[static::class] // fallback for published config
            ?? $conversionClasses[get_parent_class(static::class)] // fallback use parent class
            ?? StandardMediaDefinitions::class;
    }
}
