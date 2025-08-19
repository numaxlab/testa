<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Models\Attachment;

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

    public function instructors(): BelongsToMany
    {
        return $this
            ->belongsToMany(Instructor::class)
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
