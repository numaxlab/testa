<?php

namespace Testa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\VenueFactory;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

class Venue extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return VenueFactory::new();
    }

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
