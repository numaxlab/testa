<?php

namespace Testa\Storefront\Livewire\Components\Media;

use Illuminate\View\View;
use Livewire\Component;
use Testa\Storefront\Queries\Media\GetLatestPublicDocuments;

class LatestDocuments extends Component
{
    public function render(): View
    {
        $documents = new GetLatestPublicDocuments()->execute();

        return view('testa::storefront.livewire.components.media.latest-documents', compact('documents'));
    }
}
