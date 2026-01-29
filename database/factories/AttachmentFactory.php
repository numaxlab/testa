<?php

namespace Testa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Attachment;
use Testa\Models\Content\Tier;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Document;
use Testa\Models\Media\Video;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'attachable_type' => (new Tier)->getMorphClass(),
            'attachable_id' => Tier::factory(),
            'media_type' => (new Audio)->getMorphClass(),
            'media_id' => Audio::factory(),
            'position' => 0,
        ];
    }

    public function forAudio(): static
    {
        return $this->state(fn () => [
            'media_type' => (new Audio)->getMorphClass(),
            'media_id' => Audio::factory(),
        ]);
    }

    public function forVideo(): static
    {
        return $this->state(fn () => [
            'media_type' => (new Video)->getMorphClass(),
            'media_id' => Video::factory(),
        ]);
    }

    public function forDocument(): static
    {
        return $this->state(fn () => [
            'media_type' => (new Document)->getMorphClass(),
            'media_id' => Document::factory(),
        ]);
    }
}
