<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\Education\GetModuleAttachments;

class ModuleMedia extends Component
{
    public CourseModule $module;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = new GetModuleAttachments()
            ->execute($this->module)
            ->filter(fn($attachment) => Gate::allows('view', $attachment->media));
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.module-media');
    }
}
