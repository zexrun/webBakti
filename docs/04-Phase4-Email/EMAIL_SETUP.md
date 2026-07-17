# 📧 Email Integration & Setup Guide

**Date:** 17 August 2026  
**Status:** Phase 6, Feature #4 Implementation Complete  
**Priority:** HIGH

---

## 📋 Overview

Email Integration enables the system to send professional HTML emails for all notifications. This includes:
- Submission grading notifications
- Task deadline reminders
- Attendance approval/rejection notifications
- Exception approval/rejection notifications

---

## 🔧 Configuration

### Option 1: Gmail SMTP (Recommended for Development)

**Steps:**

1. **Enable Less Secure Apps** (for regular Gmail):
   - Go to https://myaccount.google.com/lesssecureapps
   - Turn ON "Allow less secure app access"

2. **Generate App Password** (for Gmail with 2FA - Recommended):
   - Go to https://myaccount.google.com/apppasswords
   - Select "Mail" and "Windows Computer"
   - Generate a 16-character password

3. **Update .env file:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_ENCRYPTION=tls
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-16-char-app-password
   MAIL_FROM_ADDRESS="your-email@gmail.com"
   MAIL_FROM_NAME="Sistem Monitoring Magang"
   ```

4. **Test Connection:**
   ```bash
   php artisan tinker
   > Mail::raw('Test email', function($message) { $message->to('test@example.com'); });
   ```

---

### Option 2: SendGrid (Production Recommended)

**Steps:**

1. **Create SendGrid Account:**
   - Go to https://sendgrid.com
   - Sign up and verify email
   - Create API Key

2. **Update .env:**
   ```env
   MAIL_MAILER=sendgrid
   SENDGRID_API_KEY=your-sendgrid-api-key
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="Sistem Monitoring Magang"
   ```

---

### Option 3: Mailgun

**Steps:**

1. **Create Mailgun Account:**
   - Go to https://mailgun.com
   - Sign up and verify domain
   - Get API Key and Domain

2. **Update .env:**
   ```env
   MAIL_MAILER=mailgun
   MAILGUN_DOMAIN=your-domain.mailgun.org
   MAILGUN_SECRET=your-api-key
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="Sistem Monitoring Magang"
   ```

---

### Option 4: Local Testing with Mailtrap

**Steps:**

1. **Create Mailtrap Account:**
   - Go to https://mailtrap.io
   - Sign up (free tier available)
   - Create new Inbox

2. **Copy SMTP Settings from Mailtrap Dashboard:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your-mailtrap-username
   MAIL_PASSWORD=your-mailtrap-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="Sistem Monitoring Magang"
   ```

3. **View Emails:**
   - Go to Mailtrap Dashboard
   - All sent emails appear in the Inbox
   - Perfect for development testing

---

## 📧 Notification Types & Email Templates

### 1. SubmissionGraded
**File:** `app/Notifications/SubmissionGraded.php`  
**Template:** `resources/views/emails/submission-graded.blade.php`

**Sent when:** Supervisor gives grade to student submission

**Template includes:**
- Student greeting
- Task title and supervisor name
- Grade display (large, colored)
- Comments from supervisor
- Link to view full submission
- Encouragement message

---

### 2. TaskDeadlineReminder
**File:** `app/Notifications/TaskDeadlineReminder.php`  
**Template:** `resources/views/emails/task-deadline-reminder.blade.php`

**Sent when:** Task deadline is 7 days, 1 day, or today

**Template includes:**
- Alert level based on urgency (urgent/high/medium)
- Task details (title, supervisor, type, deadline)
- Days remaining counter
- Task description preview
- Attachment info (if exists)
- Step-by-step instructions
- Call-to-action button

---

### 3. AttendanceApprovalNotification
**File:** `app/Notifications/AttendanceApprovalNotification.php`  
**Template:** `resources/views/emails/attendance-approval.blade.php`

**Sent when:** Admin approves or rejects attendance

**Template includes:**
- Approval status (approved/rejected)
- Attendance details (date, time, location)
- Location verification status
- If rejected: rejection reason and next steps
- If approved: thank you message
- Link to attendance history

---

### 4. ExceptionApprovalNotification
**File:** `app/Notifications/ExceptionApprovalNotification.php`  
**Template:** `resources/views/emails/exception-approval.blade.php`

**Sent when:** Admin approves or rejects exception (sick/leave)

**Template includes:**
- Approval status
- Exception details (date, type, reason)
- Supporting document status
- If rejected: rejection reason and reapplication options
- If approved: confirmation and impact on attendance
- Admin contact information

---

## 🔄 Scheduler Configuration

**File:** `routes/console.php`

The system automatically sends deadline reminders daily at 08:00 AM:

```php
Schedule::command('tasks:send-deadline-reminders')
    ->dailyAt('08:00')
    ->description('Send task deadline reminder notifications');
```

**To run manually:**
```bash
php artisan tasks:send-deadline-reminders
```

**To check scheduled tasks:**
```bash
php artisan schedule:list
```

---

## 🧪 Testing Email Sending

### Manual Test (Artisan Tinker):
```bash
php artisan tinker

# Send test email to yourself
> Mail::to('your@email.com')->send(new \App\Mail\TestMail());

# Send notification
> $user = \App\Models\User::first();
> $user->notify(new \App\Notifications\SubmissionGraded($submission));
```

### Send Deadline Reminders:
```bash
php artisan tasks:send-deadline-reminders
```

### View Sent Emails (if using Mailtrap):
- Go to Mailtrap Dashboard
- Check Inbox for sent emails
- Inspect full email source and HTML

---

## 📝 Email Template Structure

All emails use a base layout with consistent styling:

**File:** `resources/views/emails/layout.blade.php`

**Template Structure:**
```
Header (branding, app name)
├── Body content (extends layout)
├── Sections (organized information)
├── Alerts (info/warning/success/danger)
├── Call-to-action buttons
└── Footer (copyright, links)
```

**Custom Sections in Each Template:**
- Details information (info rows)
- Status indicators
- Action buttons
- Next steps or recommendations

---

## 🐛 Troubleshooting

### Emails Not Sending?

1. **Check .env configuration:**
   ```bash
   php artisan tinker
   > config('mail')
   ```

2. **Check mail log (if MAIL_MAILER=log):**
   ```bash
   tail -f storage/logs/laravel.log | grep -i mail
   ```

3. **Verify SMTP credentials:**
   - Test connection manually
   - Check port (usually 587 for TLS, 465 for SSL)
   - Ensure no firewall blocking

4. **Check application logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Queue Issues (if using database queue):

```bash
# Process queued jobs manually
php artisan queue:work

# Check queue status
php artisan queue:failed
```

### Gmail Authentication Failed:

- If using regular Gmail: Enable Less Secure Apps
- If using Gmail with 2FA: Use App Password (16 characters)
- Ensure password doesn't have extra spaces

---

## 📊 Email Statistics

Track sent emails via Laravel logs or mail service provider dashboard:

- **Gmail:** Check sent folder and activity log
- **SendGrid:** Dashboard shows delivery stats
- **Mailgun:** Logs tab shows all sending events
- **Mailtrap:** Inbox shows all test emails

---

## 🔐 Security Best Practices

1. **Never commit .env file to git:**
   ```
   .env is in .gitignore ✓
   ```

2. **Use environment variables:**
   ```php
   // Bad ❌
   MAIL_PASSWORD=actual-password-here
   
   // Good ✓
   MAIL_PASSWORD=env('MAIL_PASSWORD')
   ```

3. **Use App Passwords for Gmail:**
   - Never use actual Gmail password
   - Use 16-character App Password
   - Can be revoked anytime

4. **For Production:**
   - Use SendGrid or similar service
   - Don't relay through personal email account
   - Use dedicated sending domain
   - Monitor bounce rates and complaints

---

## 📱 Queue Configuration (Optional)

For better performance, queue emails:

**Update .env:**
```env
QUEUE_CONNECTION=database
```

**Create jobs table:**
```bash
php artisan queue:table
php artisan migrate
```

**Process queue:**
```bash
php artisan queue:work
```

---

## 🚀 Production Deployment

1. **Set MAIL_MAILER to production service:**
   ```env
   MAIL_MAILER=sendgrid
   SENDGRID_API_KEY=xxxxx
   ```

2. **Configure DNS records** (if using custom domain):
   - SPF record
   - DKIM record
   - DMARC policy

3. **Monitor email deliverability:**
   - Check bounce rate
   - Monitor spam complaints
   - Review unsubscribe rates

4. **Setup bounce handling** (optional):
   - Configure webhook for bounces
   - Mark invalid emails

---

## 📞 Support

**For email-related issues:**
- Check Laravel Mail documentation: https://laravel.com/docs/mail
- Check your mail service provider documentation
- Review application logs in `storage/logs/`

---

**Last Updated:** August 17, 2026  
**Maintained By:** Development Team
