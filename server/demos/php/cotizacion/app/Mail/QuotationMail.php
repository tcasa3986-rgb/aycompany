<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public string $customMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        $companyName = Setting::get('company_name', 'Mi Empresa');
        return new Envelope(
            subject: "Cotización {$this->quotation->quotation_number} — {$companyName}",
        );
    }

    public function content(): Content
    {
        $company = array_merge(Setting::defaults(), Setting::all_keyed());
        return new Content(
            view: 'emails.quotation',
            with: ['quotation' => $this->quotation, 'company' => $company, 'customMessage' => $this->customMessage],
        );
    }

    public function attachments(): array
    {
        $this->quotation->load('client', 'details.product');
        $company = array_merge(Setting::defaults(), Setting::all_keyed());
        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $this->quotation,
            'company'   => $company,
        ])->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn() => $pdf->output(), 'Cotizacion-'.$this->quotation->quotation_number.'.pdf')
                      ->withMime('application/pdf'),
        ];
    }
}
