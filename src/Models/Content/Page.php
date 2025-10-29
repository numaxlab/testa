<?php

namespace Trafikrak\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUrls;
    use LogsActivity;

    public $translatable = [
        'name',
        'intro',
        'description',
        'content',
    ];
    protected $guarded = [];

    protected $casts = [
        'section' => Section::class,
        'content' => 'array',
    ];

    public function getHasBreadcrumbAttribute(): bool
    {
        return match ($this->section) {
            Section::BOOKSHOP, Section::EDITORIAL, Section::EDUCATION => true,
            default => false,
        };
    }

    public function getBreadcrumbRouteNameAttribute(): ?string
    {
        return match ($this->section) {
            Section::BOOKSHOP => 'trafikrak.storefront.bookshop.homepage',
            Section::EDITORIAL => 'trafikrak.storefront.editorial.homepage',
            Section::EDUCATION => 'trafikrak.storefront.education.homepage',
            default => null,
        };
    }

    public function getHumanSectionAttribute(): ?string
    {
        return match ($this->section) {
            Section::BOOKSHOP => __('Librería'),
            Section::EDITORIAL => __('Editorial'),
            Section::EDUCATION => __('Formación'),
            default => null,
        };
    }
}
