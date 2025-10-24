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
}
