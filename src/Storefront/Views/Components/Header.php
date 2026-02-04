<?php

namespace Testa\Storefront\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Testa\Models\MenuItem;

class Header extends Component
{
    public function __construct() {}

    public function render(): View
    {
        $menuItems = MenuItem::where('is_published', true)
            ->whereNull('parent_id')
            ->orderBy('sort_position')
            ->with(['children'])
            ->get();

        return view('testa::components.header', compact('menuItems'));
    }
}