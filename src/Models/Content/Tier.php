<?php

namespace Trafikrak\Models\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Collection;
use Spatie\Translatable\HasTranslations;

class Tier extends Model
{
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
    ];
    protected $casts = [
        'section' => Section::class,
        'type' => TierType::class,
    ];
    protected $guarded = [];

    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(Banner::class);
    }

    public function collections(): BelongsToMany
    {
        $prefix = config('lunar.database.table_prefix');

        return $this->belongsToMany(Collection::modelClass(), "{$prefix}collection_tier");
    }
}
