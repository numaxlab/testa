<?php

namespace Testa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Testa\Database\Factories\AttachmentFactory;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Document;
use Testa\Models\Media\Video;

class Attachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }

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
