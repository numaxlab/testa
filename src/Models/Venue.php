<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\News\Event;

class Venue extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
    ];
    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function courseModules(): HasMany
    {
        return $this->hasMany(CourseModule::class);
    }
}
