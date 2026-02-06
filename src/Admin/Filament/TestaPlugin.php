<?php

namespace Testa\Admin\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Testa\Admin\Filament\Pages\ContactSettings;
use Testa\Admin\Filament\Pages\PaymentSettings;
use Testa\Admin\Filament\Resources\Config\MenuItemResource;
use Testa\Admin\Filament\Resources\Content\BannerResource;
use Testa\Admin\Filament\Resources\Content\PageResource;
use Testa\Admin\Filament\Resources\Content\SlideResource;
use Testa\Admin\Filament\Resources\Content\TierResource;
use Testa\Admin\Filament\Resources\Education\CourseModuleResource;
use Testa\Admin\Filament\Resources\Education\CourseResource;
use Testa\Admin\Filament\Resources\Education\TopicResource;
use Testa\Admin\Filament\Resources\Education\VenueResource;
use Testa\Admin\Filament\Resources\Media\AudioResource;
use Testa\Admin\Filament\Resources\Media\DocumentResource;
use Testa\Admin\Filament\Resources\Media\VideoResource;
use Testa\Admin\Filament\Resources\Membership\BenefitResource;
use Testa\Admin\Filament\Resources\Membership\MembershipPlanResource;
use Testa\Admin\Filament\Resources\Membership\MembershipTierResource;
use Testa\Admin\Filament\Resources\News\ArticleResource;
use Testa\Admin\Filament\Resources\News\EventResource;
use Testa\Admin\Filament\Resources\News\EventTypeResource;

class TestaPlugin implements Plugin
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
        return 'testa';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CourseResource::class,
            CourseModuleResource::class,
            TopicResource::class,
            VenueResource::class,
            AudioResource::class,
            DocumentResource::class,
            VideoResource::class,
            MembershipTierResource::class,
            MembershipPlanResource::class,
            BenefitResource::class,
            TierResource::class,
            SlideResource::class,
            BannerResource::class,
            PageResource::class,
            ArticleResource::class,
            EventTypeResource::class,
            EventResource::class,
            MenuItemResource::class,
        ]);

        $panel->pages([
            ContactSettings::class,
            PaymentSettings::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
