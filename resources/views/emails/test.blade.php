@component('mail::message')
# 📧 Email Sandbox Test

**{{ $appName }}** - Email Test Email

---

## Test Details

**Timestamp:** {{ $timestamp }}
**App URL:** {{ $appUrl }}
**Message:** {{ $message }}

---

## ✅ Success!

This email was successfully sent through Mailtrap sandbox.

**Next Steps:**
1. Check your [Mailtrap Dashboard](https://mailtrap.io) inbox
2. Review email headers, body, and attachments
3. Test other email features (notifications, password reset, etc.)

---

## Testing Features

You can now test:
- ✅ Email delivery
- ✅ Email formatting
- ✅ Attachments
- ✅ HTML rendering
- ✅ Plain text fallback
- ✅ Email headers

---

@component('mail::footer')
WebBakti System - Email Sandbox Testing
Environment: {{ config('app.env') }}
@endcomponent
@endcomponent
