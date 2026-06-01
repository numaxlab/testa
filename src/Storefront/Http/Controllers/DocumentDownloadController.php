<?php

namespace Testa\Storefront\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Testa\Models\Media\Document;

class DocumentDownloadController
{
    public function __invoke(Request $request, Document $document): mixed
    {
        Gate::authorize('view', $document);

        return Storage::response($document->path, $document->name);
    }
}
