<?php

namespace Trafikrak\Admin\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Trafikrak\Admin\Filament\Resources\Education\CourseModuleResource;
use Trafikrak\Admin\Filament\Resources\Education\CourseResource;
use Trafikrak\Admin\Filament\Resources\Education\InstructorResource;
use Trafikrak\Admin\Filament\Resources\Education\TopicResource;
use Trafikrak\Admin\Filament\Resources\Media\AudioResource;
use Trafikrak\Admin\Filament\Resources\Media\DocumentResource;
use Trafikrak\Admin\Filament\Resources\Media\VideoResource;

class TrafikrakPlugin implements Plugin
{
    public static function get(): static
    {
        return static::make();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'trafikrak';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CourseResource::class,
            CourseModuleResource::class,
            TopicResource::class,
            InstructorResource::class,
            AudioResource::class,
            DocumentResource::class,
            VideoResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
