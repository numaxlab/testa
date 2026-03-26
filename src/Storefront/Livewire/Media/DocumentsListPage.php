<?php

namespace Testa\Storefront\Livewire\Media;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Media\GetPublishedDocuments;

class DocumentsListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $c = '';

    #[Url]
    public string $t = '';

    public function render(): View
    {
        $documents = new GetPublishedDocuments()->execute();

        return view('testa::storefront.livewire.media.documents-list', compact('documents'))
            ->title(__('Documentos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
