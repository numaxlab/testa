<?php

namespace Trafikrak\Storefront\Livewire\Components\Media;

use Illuminate\View\View;
use Livewire\Component;
use Trafikrak\Models\Media\Document;

class LatestDocuments extends Component
{
    public function render(): View
    {
        $documents = Document::where('is_published', true)->latest()->take(6)->get();

        return view('trafikrak::storefront.livewire.components.media.latest-documents', compact('documents'));
    }
}
