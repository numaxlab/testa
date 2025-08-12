<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class CourseModule extends Model
{
    use HasUrls;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'subtitle',
        'description',
    ];
    protected $casts = [
        'starts_at' => 'datetime',
    ];
    protected $guarded = [];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
