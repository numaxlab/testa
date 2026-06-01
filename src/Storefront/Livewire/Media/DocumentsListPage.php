<?php

namespace Testa\Storefront\Livewire\Media;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Media\GetAccessibleDocuments;

class DocumentsListPage extends Page
{
    use WithPagination;

    public function render(): View
    {
        $customer = Auth::check() ? Auth::user()->latestCustomer() : null;

        $documents = new GetAccessibleDocuments()->execute(customer: $customer);

        return view('testa::storefront.livewire.media.documents-list', compact('documents'))
            ->title(__('Documentos'));
    }
}
