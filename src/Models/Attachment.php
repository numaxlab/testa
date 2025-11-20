<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trafikrak\Models\Media\Audio;
use Trafikrak\Models\Media\Document;
use Trafikrak\Models\Media\Video;

class Attachment extends Model
{
    protected $guarded = [];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function media(): MorphTo
    {
        return $this->morphTo();
    }

    public function getNameAttribute(): string
    {
        return $this->media->name;
    }

    public function getComponentNamespaceAttribute(): string
    {
        return match ($this->media_type) {
            (new Video)->getMorphClass() => 'videos',
            (new Audio)->getMorphClass() => 'audios',
            (new Document)->getMorphClass() => 'documents',
            default => throw new \RuntimeException('Unsupported media type'),
        };
    }
}
