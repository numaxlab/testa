<x-mail::message>
# {{ __('testa::mail.admin_order_notification.heading', ['reference' => $order->reference]) }}

**{{ __('testa::mail.admin_order_notification.date') }}:** {{ $order->created_at->format('d/m/Y H:i') }}

**{{ __('testa::mail.admin_order_notification.customer') }}:**
{{ $order->billingAddress?->first_name }} {{ $order->billingAddress?->last_name }}
({{ $order->billingAddress?->contact_email ?? $order->user?->email }})

**{{ __('testa::mail.admin_order_notification.payment_method') }}:** {{ $order->payment_type ?? '—' }}

---

<x-mail::table>
| {{ __('testa::mail.order_lines.product') }} | {{ __('testa::mail.order_lines.qty') }} | {{ __('testa::mail.order_lines.price') }} |
| :-- | --: | --: |
@foreach ($order->lines as $line)
| {{ $line->description }} | {{ $line->quantity }} | {{ $line->total->formatted }} |
@endforeach
</x-mail::table>

**{{ __('testa::mail.order_lines.total') }}: {{ $order->total->formatted }}**

<x-mail::button :url="config('app.url').'/'.config('lunar.admin.prefix', 'lunar').'/orders/'.$order->id">
{{ __('testa::mail.admin_order_notification.view_order') }}
</x-mail::button>
</x-mail::message>
