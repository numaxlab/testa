<?php

namespace Trafikrak\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Trafikrak\Models\Attachment;

class Video extends Model
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'media');
    }
}
