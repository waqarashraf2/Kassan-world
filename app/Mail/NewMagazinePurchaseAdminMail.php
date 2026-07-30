<?php

namespace App\Mail;

use App\Models\MagazinePurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMagazinePurchaseAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MagazinePurchase $purchase) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New KISANWORLD magazine purchase - '.$this->purchase->purchase_number);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-new-magazine-purchase');
    }
}
