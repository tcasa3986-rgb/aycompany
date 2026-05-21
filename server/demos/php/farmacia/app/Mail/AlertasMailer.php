<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertasMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $bajoStock;
    public $porVencer;

    public function __construct($bajoStock, $porVencer)
    {
        $this->bajoStock = $bajoStock;
        $this->porVencer = $porVencer;
    }

    public function build()
    {
        return $this->subject('Reporte de Alertas: Stock Bajo y Vencimientos')
                    ->view('emails.alertas');
    }
}
