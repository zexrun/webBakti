# Email Configuration & Testing Guide

## Overview

WebBakti supports multiple email providers for sending notifications, password resets, and messages. This guide covers setup and testing with **Mailtrap** (recommended sandbox for development).

## Email Providers Comparison

| Provider | Purpose | Setup Time | Cost | Best For |
|----------|---------|-----------|------|----------|
| **Mailtrap** | Development/Testing | 2 min | Free tier | Development, testing, staging |
| **Gmail** | Production/Testing | 5 min | Free/Paid | Small volume, personal use |
| **SendGrid** | Production | 5 min | Paid | High volume, production |
| **AWS SES** | Production | 10 min | Pay-per-email | AWS infrastructure |
| **MailHog** | Local testing | 5 min | Free | Local development only |

## Setup Guide

### Option 1: Mailtrap (Recommended for Development)

#### 1. Create Mailtrap Account

1. Go to [mailtrap.io](https://mailtrap.io)
2. Sign up (free account)
3. Verify email
4. Login to dashboard

#### 2. Get SMTP Credentials

1. Click "Demo Inbox" (auto-created)
2. Click "Settings" tab
3. Copy these values:
   - **SMTP Host:** `smtp.mailtrap.io`
   - **SMTP Port:** `2525`
   - **Username:** `xxxxxxxxxxxxxxxx` (alphanumeric ID)
   - **Password:** `xxxxxxxxxxxxxxxx` (random string)
   - **Authentication:** PLAIN, LOGIN, CRAM-MD5
   - **TLS/SSL:** Required

#### 3. Update .env

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@webbakti.local
MAIL_FROM_NAME="WebBakti System"
```

#### 4. Test Connection

```bash
php artisan email:test --to=admin@webbakti.local
```

**Expected output:**
```
✅ Email sent successfully!
```

#### 5. Verify in Mailtrap

1. Go to [mailtrap.io Dashboard](https://mailtrap.io)
2. Click "Demo Inbox"
3. You should see your test email
4. Click to view full email details (headers, body, attachments, etc.)

---

### Option 2: Gmail

#### 1. Generate App Password

1. Go to [myaccount.google.com](https://myaccount.google.com)
2. Click "Security" (left sidebar)
3. Enable "2-Step Verification" if not already enabled
4. Scroll down → "App passwords"
5. Select "Mail" and "Windows Computer"
6. Google will generate 16-character password

#### 2. Update .env

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=rifqykhuzaini@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=rifqykhuzaini@gmail.com
MAIL_FROM_NAME="WebBakti System"
```

#### 3. Test Connection

```bash
php artisan email:test --to=recipient@example.com
```

---

### Option 3: MailHog (Local Only)

For local testing without external service.

#### 1. Install & Run MailHog

```bash
# Download from: https://github.com/mailhog/MailHog/releases
# Or use Docker:
docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog
```

#### 2. Update .env

```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@webbakti.local
MAIL_FROM_NAME="WebBakti System"
```

#### 3. View Emails

Open: [http://localhost:8025](http://localhost:8025)

---

## Testing Email Functionality

### 1. Send Test Email

```bash
# Basic test
php artisan email:test

# Test with custom recipient
php artisan email:test --to=student@example.com

# Test with custom message
php artisan email:test --to=admin@webbakti.local --message="Custom test message"
```

### 2. Test Notifications

Laravel notifications automatically use configured mail driver:

```bash
# In Tinker or artisan command
php artisan tinker

# Send notification to user
>>> $user = User::first();
>>> $user->notify(new NotificationClass());
>>> exit
```

### 3. Test Password Reset Email

1. Go to login page
2. Click "Forgot Password"
3. Enter user email
4. Check Mailtrap inbox for reset email
5. Click reset link

### 4. Verify Email Features

Check these aspects in Mailtrap inbox:

- ✅ **Subject line** - Correct and descriptive
- ✅ **From address** - Configured correctly
- ✅ **HTML rendering** - Proper formatting, no broken layouts
- ✅ **Plain text fallback** - Works without HTML
- ✅ **Links** - All clickable and correct URLs
- ✅ **Attachments** - If included, appears correctly
- ✅ **Headers** - SPF, DKIM, DMARC configured
- ✅ **Responsive** - Looks good on mobile

---

## Email Features

### Notifications

System can send notifications for:
- ✅ User registration
- ✅ Password reset
- ✅ Email verification
- ✅ Task assignment
- ✅ Logbook feedback
- ✅ Grade updates
- ✅ Message received
- ✅ Attendance exceptions

### Creating Custom Email

```php
// Create Mailable class
php artisan make:mail CustomEmail

// In app/Mail/CustomEmail.php
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Email Subject',
    );
}

public function content(): Content
{
    return new Content(
        view: 'emails.custom',
        with: [
            'data' => $this->data,
        ],
    );
}
```

### Sending Email

```php
use App\Mail\CustomEmail;
use Illuminate\Support\Facades\Mail;

Mail::to('user@example.com')->send(new CustomEmail($data));

// Queue for later
Mail::to('user@example.com')->queue(new CustomEmail($data));

// CC/BCC
Mail::to('user@example.com')
    ->cc('manager@example.com')
    ->bcc('admin@example.com')
    ->send(new CustomEmail($data));
```

---

## Troubleshooting

### "Connection refused" error

```
SMTP connect() failed
```

**Solutions:**
- Check MAIL_HOST is correct (no typo)
- Check MAIL_PORT is correct (2525 for Mailtrap, 587 for Gmail, 25 for local)
- Check firewall not blocking connection
- Verify internet connection working
- Try: `telnet smtp.mailtrap.io 2525` to test connectivity

### "Authentication failed" error

```
SMTP Error: Could not authenticate
```

**Solutions:**
- Double-check MAIL_USERNAME (copy from Mailtrap, not email)
- Double-check MAIL_PASSWORD (should be random string, not your password)
- Check for extra spaces or hidden characters in .env
- Regenerate credentials in Mailtrap if unsure

### "Certificate verification failed"

```
stream_socket_enable_crypto(): Peer certificate cannot be authenticated
```

**Solutions:**
- Verify MAIL_ENCRYPTION is set to `tls` or `ssl`
- Check OpenSSL installed on server
- Update certificates: `php artisan config:clear`

### Email not appearing in inbox

**Check:**
1. ✅ Verify recipient address is correct
2. ✅ Check spam/junk folder
3. ✅ Confirm MAIL_FROM_ADDRESS is configured
4. ✅ Run: `php artisan config:clear` then retry
5. ✅ Check Laravel logs: `storage/logs/laravel.log`
6. ✅ Test again: `php artisan email:test --to=your-email@gmail.com`

---

## Production Deployment

### Before Going Live

- [ ] Update MAIL_FROM_ADDRESS to official domain
- [ ] Update MAIL_FROM_NAME to official company name
- [ ] Switch from Mailtrap to production service (SendGrid, SES, etc.)
- [ ] Test with real email addresses
- [ ] Configure SPF/DKIM/DMARC records
- [ ] Set up email bounce handling
- [ ] Monitor email delivery rates
- [ ] Test email in various clients (Gmail, Outlook, Apple Mail, etc.)

### Recommended Production Setup

**SendGrid** (if scaling):
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@baktikominfo.id
MAIL_FROM_NAME="WebBakti - BAKTI"
```

**AWS SES** (if using AWS):
```env
MAIL_MAILER=ses
AWS_REGION=ap-southeast-1
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
MAIL_FROM_ADDRESS=noreply@baktikominfo.id
```

---

## Laravel Queue Integration

For high-volume emails, use queue:

```php
// In config/queue.php, set to 'database' or 'redis'
QUEUE_CONNECTION=database

// Queue migrations
php artisan queue:table
php artisan migrate

// Queue worker
php artisan queue:work
```

Then use `queue()` instead of `send()`:

```php
Mail::to('user@example.com')->queue(new CustomEmail($data));
```

---

## Commands Reference

| Command | Purpose |
|---------|---------|
| `php artisan email:test` | Send test email |
| `php artisan make:mail ClassName` | Create new mailable |
| `php artisan queue:work` | Process queued emails |
| `php artisan queue:failed` | List failed emails |
| `php artisan queue:retry` | Retry failed emails |

---

## Additional Resources

- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Mailtrap Documentation](https://mailtrap.io/blog/laravel-send-email/)
- [SendGrid Laravel Integration](https://sendgrid.com/docs/for-developers/sending-email/laravel/)
- [AWS SES Laravel Integration](https://aws.amazon.com/blogs/messaging-and-targeting/sending-email-with-amazon-ses-through-your-laravel-application/)

---

## Questions?

1. **Email not sending?** → Check MAIL_* config in .env
2. **Not in Mailtrap inbox?** → Verify SMTP credentials, check Laravel logs
3. **Want to test notifications?** → Use `php artisan tinker`
4. **Production setup help?** → See "Production Deployment" section above
