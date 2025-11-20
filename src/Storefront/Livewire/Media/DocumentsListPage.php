<?php

namespace Trafikrak\Storefront\Livewire\Media;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Media\Document;

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
        $documents = Document::where('is_published', true)
            ->paginate(16);

        return view('trafikrak::storefront.livewire.media.documents-list', compact('documents'))
            ->title(__('Documentos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
