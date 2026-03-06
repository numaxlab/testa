<?php

namespace Testa\Admin\Filament\Resources\News\ArticleResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageMediasRelatedRecords;
use Testa\Admin\Filament\Resources\News\ArticleResource;

class ManageArticleMedia extends ManageMediasRelatedRecords
{
    protected static string $resource = ArticleResource::class;
}
