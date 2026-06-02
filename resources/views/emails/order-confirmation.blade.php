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

    @if ($order->shippingAddress || $order->billingAddress)
        ---

        @if ($order->shippingAddress)
            **{{ __('testa::mail.order_confirmation.shipping_address') }}**

            {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
            {{ $order->shippingAddress->line_one }}
            @if ($order->shippingAddress->line_two)
                {{ $order->shippingAddress->line_two }}
            @endif
            {{ $order->shippingAddress->postcode }} {{ $order->shippingAddress->city }}
            {{ $order->shippingAddress->country->name ?? '' }}

        @endif
        @php($showBilling = $order->billingAddress && (
            ! $order->shippingAddress ||
            $order->billingAddress->line_one !== $order->shippingAddress->line_one ||
            $order->billingAddress->postcode !== $order->shippingAddress->postcode
        ))
        @if ($showBilling)
            **{{ __('testa::mail.order_confirmation.billing_address') }}**

            {{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}
            {{ $order->billingAddress->line_one }}
            @if ($order->billingAddress->line_two)
                {{ $order->billingAddress->line_two }}
            @endif
            {{ $order->billingAddress->postcode }} {{ $order->billingAddress->city }}
            {{ $order->billingAddress->country->name ?? '' }}
        @endif
    @endif

    {{ __('testa::mail.order_confirmation.footer') }}
</x-mail::message>
