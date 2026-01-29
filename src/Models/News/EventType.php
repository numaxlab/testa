<?php

namespace Testa\Models\News;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\News\EventTypeFactory;

class EventType extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return EventTypeFactory::new();
    }

    public $translatable = [
        'name',
    ];
    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
