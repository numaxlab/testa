<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function media(): MorphTo
    {
        return $this->morphTo();
    }
}
