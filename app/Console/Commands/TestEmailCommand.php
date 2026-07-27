<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {--to=admin@webbakti.local : Email recipient} {--message="Test email from WebBakti" : Custom message}';

    protected $description = 'Send a test email via configured mail driver (Mailtrap, Gmail, etc)';

    public function handle()
    {
        $to = $this->option('to');
        $message = $this->option('message');

        $this->info('📧 Sending test email...');
        $this->line('To: ' . $to);
        $this->line('Message: ' . $message);
        $this->line('');

        try {
            Mail::to($to)->send(new TestEmail($message));

            $this->info('✅ Email sent successfully!');
            $this->line('');
            $this->line('📌 Check your email inbox:');
            $this->line('   If using Mailtrap: https://mailtrap.io → Demo Inbox');
            $this->line('   If using Gmail: Gmail inbox');
            $this->line('   Subject: WebBakti Email Sandbox Test - ' . now()->format('Y-m-d H:i:s'));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email');
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->line('🔧 Troubleshooting:');
            $this->line('1. Check MAIL_* config in .env');
            $this->line('2. Verify SMTP credentials (host, port, username, password)');
            $this->line('3. Check if MAIL_ENCRYPTION is correct (tls, ssl, or null)');
            $this->line('4. Run: php artisan config:clear');

            return Command::FAILURE;
        }
    }
}
