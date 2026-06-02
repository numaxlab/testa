<?php

namespace Testa\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;
use Testa\Settings\EmailSettings;

class OrderPendingPaymentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('testa::mail.order_pending_payment.subject', ['reference' => $this->order->reference]),
        );
    }

    public function content(): Content
    {
        $settings = app(EmailSettings::class);

        return new Content(
            markdown: 'testa::emails.order-pending-payment',
            with: [
                'greeting' => $settings->getOrderPendingPaymentGreeting(),
                'intro' => $settings->getOrderPendingPaymentIntro(),
            ],
        );
    }
}
