<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $testMessage;
    public string $timestamp;

    public function __construct(string $testMessage = 'This is a test email')
    {
        $this->testMessage = $testMessage;
        $this->timestamp = now()->format('Y-m-d H:i:s');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'WebBakti Email Sandbox Test - ' . $this->timestamp,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
            with: [
                'message' => $this->testMessage,
                'timestamp' => $this->timestamp,
                'appName' => config('app.name'),
                'appUrl' => config('app.url'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
