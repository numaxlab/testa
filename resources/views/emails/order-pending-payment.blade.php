<x-mail::message>
    # {{ $greeting }}

    {{ $intro }}

    <x-mail::table>
        | {{ __('testa::mail.order_lines.product') }} | {{ __('testa::mail.order_lines.qty') }}
        | {{ __('testa::mail.order_lines.price') }} |
        | :-- | --: | --: |
        @foreach ($order->lines as $line)
            | {{ $line->description }} | {{ $line->quantity }} | {{ $line->total->formatted }} |
        @endforeach
    </x-mail::table>

    **{{ __('testa::mail.order_lines.total') }}: {{ $order->total->formatted }}**

    {{ __('testa::mail.order_pending_payment.footer') }}
</x-mail::message>
