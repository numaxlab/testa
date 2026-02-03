<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\View\View;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class CourseRegisterSuccessPage extends Page
{
    public Order $order;

    public function mount($fingerprint): void
    {
        $this->order = Order::where('fingerprint', $fingerprint)
            ->whereNotNull('placed_at')
            ->firstOrFail();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.education.course-register-success')
            ->title(__('Inscripción en curso completada'));
    }
}
