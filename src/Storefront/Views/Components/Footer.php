<?php

namespace Testa\Storefront\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Testa\Models\MenuItem;

class Footer extends Component
{
    public function render(): View
    {
        $menuItems = MenuItem::where('is_published', true)
            ->whereNull('parent_id')
            ->orderBy('sort_position')
            ->get();

        return view('testa::components.footer', compact('menuItems'));
    }
}
