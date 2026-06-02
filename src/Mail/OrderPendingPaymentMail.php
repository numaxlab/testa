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

    public function __construct(public Order $order, public EmailSettings $settings)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->settings->getOrderPendingPaymentGreeting(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'testa::emails.order-pending-payment',
            with: [
                'greeting' => $this->settings->getOrderPendingPaymentGreeting(),
                'intro' => $this->settings->getOrderPendingPaymentIntro(),
            ],
        );
    }
}
