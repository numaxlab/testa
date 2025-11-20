<article class="border-t border-b border-primary">
    <header class="flex gap-4 items-center justify-between py-2 border-b border-primary">
        <a href="{{ route('orders.show', $order->reference) }}" wire:navigate class="flex items-center gap-2">
            <i class="icon icon-shopping-bag" aria-hidden="true"></i>
            <h3 class="font-bold">
                {{ $order->reference }} |
                {{ $order->created_at->format('d/m/Y') }} |
                {{ $order->total->formatted() }}
            </h3>
        </a>
        <span class="at-tag is-primary at-small">{{ $order->status_label }}</span>
    </header>

    <ul class="divide-y divide-black">
        @foreach ($order->productLines as $line)
            <li class="flex gap-4 items-center py-2">
                <i class="icon icon-book" aria-hidden="true"></i>

                {{ $line->purchasable->getDescription() }} x {{ $line->quantity }}
            </li>
        @endforeach
    </ul>
</article>