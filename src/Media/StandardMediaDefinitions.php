<?php

namespace Trafikrak\Media;

use Lunar\Base\MediaDefinitionsInterface;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StandardMediaDefinitions implements MediaDefinitionsInterface
{
    public function registerMediaCollections(HasMedia $model): void
    {
        $fallbackUrl = config('lunar.media.fallback.url');
        $fallbackPath = config('lunar.media.fallback.path');

        // Reset to avoid duplication
        $model->mediaCollections = [];

        $collection = $model->addMediaCollection(
            config('lunar.media.collection'),
        );

        if ($fallbackUrl) {
            $collection = $collection->useFallbackUrl($fallbackUrl);
        }

        if ($fallbackPath) {
            $collection = $collection->useFallbackPath($fallbackPath);
        }

        $this->registerCollectionConversions($collection, $model);
    }

    protected function registerCollectionConversions(MediaCollection $collection, HasMedia $model): void
    {
        $conversions = [
            'zoom' => [
                'width' => 1200,
            ],
            'large' => [
                'width' => 1000,
            ],
            'medium' => [
                'width' => 700,
            ],
        ];

        $collection->registerMediaConversions(function (Media $media) use ($model, $conversions) {
            foreach ($conversions as $key => $conversion) {
                $model
                    ->addMediaConversion($key)
                    ->fit(
                        Fit::Contain,
                        $conversion['width'],
                    )
                    ->keepOriginalImageFormat()
                    ->withResponsiveImages();
            }
        });
    }

    public function registerMediaConversions(HasMedia $model, ?Media $media = null): void
    {
        $model
            ->addMediaConversion('small')
            ->fit(
                fit: Fit::Contain,
                desiredWidth: 300,
            )
            ->sharpen(10)
            ->keepOriginalImageFormat();
    }

    public function getMediaCollectionTitles(): array
    {
        return [
            config('lunar.media.collection') => __('lunar::base.standard-media-definitions.collection-titles.images'),
        ];
    }

    public function getMediaCollectionDescriptions(): array
    {
        return [
            config('lunar.media.collection') => '',
        ];
    }
}
