<x-mail::message>
# {{ __('testa::mail.order_confirmation.greeting') }}

{{ __('testa::mail.order_confirmation.intro', ['reference' => $order->reference]) }}

<x-mail::table>
| {{ __('testa::mail.order_lines.product') }} | {{ __('testa::mail.order_lines.qty') }} | {{ __('testa::mail.order_lines.price') }} |
| :-- | --: | --: |
@foreach ($order->lines as $line)
| {{ $line->description }} | {{ $line->quantity }} | {{ $line->total->formatted }} |
@endforeach
</x-mail::table>

**{{ __('testa::mail.order_lines.total') }}: {{ $order->total->formatted }}**

@if ($order->shippingAddress)
---

**{{ __('testa::mail.order_confirmation.shipping_address') }}**

{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
{{ $order->shippingAddress->line_one }}
@if ($order->shippingAddress->line_two)
{{ $order->shippingAddress->line_two }}
@endif
{{ $order->shippingAddress->postcode }} {{ $order->shippingAddress->city }}
{{ $order->shippingAddress->country->name ?? '' }}
@endif

{{ __('testa::mail.order_confirmation.footer') }}
</x-mail::message>
