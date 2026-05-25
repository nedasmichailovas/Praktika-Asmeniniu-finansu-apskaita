<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $filename;

    public function __construct($pdf, $filename = 'ataskaita.pdf')
    {
        $this->pdf      = $pdf;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Finansų ataskaita')
            ->view('emails.report')
            ->attachData($this->pdf, $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}