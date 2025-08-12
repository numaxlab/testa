<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;

class Topic extends Model implements SpatieHasMedia
{
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

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
