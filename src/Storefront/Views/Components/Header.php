<?php

namespace Testa\Storefront\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Testa\Models\MenuItem;
use Testa\Settings\ContactSettings;

class Header extends Component
{
    public ContactSettings $contactSettings;

    public function __construct(ContactSettings $contactSettings)
    {
        $this->contactSettings = $contactSettings;
    }

    public function render(): View
    {
        $menuItems = MenuItem::where('is_published', true)
            ->whereNull('parent_id')
            ->orderBy('sort_position')
            ->with(['publishedChildren.publishedChildren'])
            ->get();

        return view('testa::components.header', compact('menuItems'));
    }
}