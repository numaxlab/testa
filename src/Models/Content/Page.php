<?php

namespace Testa\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Content\PageFactory;

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

    protected static function newFactory()
    {
        return PageFactory::new();
    }

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
            Section::BOOKSHOP => 'testa.storefront.bookshop.homepage',
            Section::EDITORIAL => 'testa.storefront.editorial.homepage',
            Section::EDUCATION => 'testa.storefront.education.homepage',
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

    public function getUrlAttribute(): string
    {
        return route($this->getRouteName(), ['slug' => $this->defaultUrl->slug]);
    }

    public function getRouteName(): string
    {
        return match ($this->section) {
            Section::BOOKSHOP => 'testa.storefront.bookshop.page',
            Section::EDITORIAL => 'testa.storefront.editorial.page',
            Section::EDUCATION => 'testa.storefront.education.page',
            default => 'testa.storefront.info.page',
        };
    }

    protected function casts(): array
    {
        return [
            'section' => Section::class,
            'content' => 'array',
        ];
    }
}
