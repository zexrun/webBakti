WebBakti Email Sandbox Test

Hello,

{{ $message }}

---

Test Details:
- Sent at: {{ now()->format('Y-m-d H:i:s') }}
- Environment: {{ config('app.env') }}
- Application: {{ config('app.name') }}
- URL: {{ config('app.url') }}

---

Success! This email was successfully sent through your mail driver.

Next Steps:
1. Check your Mailtrap inbox at https://mailtrap.io/dashboard
2. Review the email content, headers, and formatting
3. Test other email features (password reset, notifications, etc.)

You can now test:
- Email delivery
- Email formatting
- Attachments
- HTML rendering
- Email headers & metadata

---

WebBakti System
Email Sandbox Testing
