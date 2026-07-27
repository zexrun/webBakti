<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TestEmail extends Mailable
{
    public function __construct(
        public string $message = 'This is a test email from WebBakti'
    ) {}

    public function build()
    {
        return $this
            ->subject('WebBakti Email Sandbox Test - ' . now()->format('Y-m-d H:i:s'))
            ->view('emails.test_simple');
    }
}
