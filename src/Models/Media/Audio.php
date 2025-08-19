<?php

namespace Trafikrak\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Base\Traits\Searchable;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Models\Attachment;

class Audio extends Model
{
    use HasTranslations;
    use HasUrls;
    use LogsActivity;
    use Searchable;

    public $translatable = [
        'name',
        'description',
    ];
    protected $table = 'audios';
    protected $guarded = [];

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'media');
    }
}
