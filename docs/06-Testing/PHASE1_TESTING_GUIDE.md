# Phase 1: CRITICAL Security Fixes - Testing Guide

**Date:** 17 August 2026  
**Version:** 1.0  
**Target:** QA Team / Testing

---

## 🚀 QUICK START

**Prerequisites:**
- Database migrated: `php artisan migrate`
- Dev server running: `npm run dev` (in separate terminal)
- `.env` has `SESSION_ENCRYPT=true`

---

## 🧪 TEST SCENARIOS

### 1. FILE UPLOAD SECURITY

#### Test 1.1: Valid File Upload
```
Step 1: Login as Student
Step 2: Go to Documents → Upload
Step 3: Upload valid PDF/Word file
Step 4: Submit

Expected Result: ✅
- File uploaded successfully
- Message: "Dokumen berhasil diunggah"
- File stored in storage/app/private (NOT public)
- Filename is UUID (e.g., 550e8400-e29b-41d4-a716-446655440000.pdf)
- Database shows mime_type, file_size, original_filename populated
```

**How to verify storage:**
```bash
# Check file exists in private storage
ls storage/app/private/documents/2026/08/17/

# Check database
php artisan tinker
>>> DB::table('documents')->latest()->first();
# Should show: mime_type, file_size, original_filename
```

---

#### Test 1.2: Malicious File Rejection (EXE)
```
Step 1: Login as Student
Step 2: Go to Documents → Upload
Step 3: Try to upload file.exe (or rename .exe to .pdf)
Step 4: Submit

Expected Result: ❌ REJECTED
- Error message: "File type tidak diizinkan..."
- File NOT saved in storage
- Database unchanged
```

**Tools to test:**
- Create test file: `echo "test" > malicious.exe`
- Create fake PDF: `echo "%PDF-fake" > notpdf.pdf` (should work - valid PDF structure)
- Test with actual executable disguised as PDF

---

#### Test 1.3: Path Traversal Prevention
```
Step 1: Intercept file upload request (Burp/DevTools)
Step 2: Change filename to: ../../../etc/passwd.pdf
Step 3: Submit

Expected Result: ❌ PREVENTED
- File still stored with UUID (traversal ignored)
- Stored in documents/{date}/ directory only
- Cannot access parent directories
```

---

#### Test 1.4: File Deletion Cleanup
```
Step 1: Login as Student
Step 2: Upload a document
Step 3: Note the filename from storage
Step 4: Go to Documents, delete the document
Step 5: Check storage

Expected Result: ✅
- Document deleted from database
- File deleted from storage/app/private/
- No orphaned files left behind
```

---

### 2. AUTHORIZATION CHECKS (IDOR)

#### Test 2.1: Supervisor Cannot Approve Other's Students
```
Setup:
- Supervisor A has Student X, Student Y
- Supervisor B has Student Z

Test:
Step 1: Login as Supervisor A
Step 2: Create attendance record for Student X
Step 3: Logout, login as Supervisor B
Step 4: Try to access: GET /supervisor/attendance/approvals
Step 5: Try to approve Student X's attendance with URL trick: 
        POST /admin/attendance/approve/attendance/[StudentX's attendance ID]

Expected Result: ❌ FORBIDDEN (403)
- Error: "Anda tidak berhak mengapprove attendance student ini."
- Attendance NOT changed
- No record of approval
```

**How to test with curl:**
```bash
# Login as Supervisor B
curl -X POST http://localhost:8000/login \
  -d "email=supervisor_b@test.com&password=password"

# Try to approve Supervisor A's student (should fail)
curl -X POST http://localhost:8000/admin/attendance/approve/attendance/123 \
  -H "X-CSRF-TOKEN: {token}" \
  -d "action=approve&notes=test"
# Should return 403 Forbidden
```

---

#### Test 2.2: Assessment Edit Authorization
```
Setup:
- Supervisor A supervises Student X
- Supervisor B supervises different Student

Test:
Step 1: Login as Supervisor A
Step 2: Go to Students → Student X → Assessment
Step 3: Edit assessment (should work) ✅
Step 4: Note Student X's ID
Step 5: Logout, login as Supervisor B
Step 6: Try direct URL: GET /supervisor/students/{StudentX_ID}/assessment/edit

Expected Result: ❌ FORBIDDEN (403)
- Error: "AKSES DITOLAK"
- Page not displayed
```

---

#### Test 2.3: Admin Can Always Approve
```
Setup:
- Any attendance/exception record exists

Test:
Step 1: Login as Admin
Step 2: Go to Attendance → Approvals
Step 3: Approve attendance from any student
Step 4: Submit

Expected Result: ✅
- Approval successful
- approved_by shows Admin's ID
- approved_at timestamp recorded
```

---

### 3. SESSION ENCRYPTION

#### Test 3.1: Session Data Encrypted
```
Step 1: Login to application
Step 2: Open database: storage/database.sqlite
Step 3: Query: SELECT * FROM sessions;
Step 4: Check payload column

Expected Result: ✅ ENCRYPTED
- Payload should NOT be readable plain text
- Should look like: TWpjMjYwNTIzMTAyNDEz... (base64 encrypted)
- NOT like: s:7:"user_id";i:1;...
```

**Verify encryption:**
```bash
php artisan tinker
>>> DB::table('sessions')->latest()->first();
# payload should look encrypted/binary, not plain text
```

---

#### Test 3.2: Session Timeout
```
Step 1: Login to application
Step 2: Note current time
Step 3: Wait 121 minutes (SESSION_LIFETIME=120)
Step 4: Try to navigate to dashboard

Expected Result: ❌ REDIRECTED TO LOGIN
- Session expired after 120 minutes
- Redirected to login page with message (if set)
```

**For testing (can't wait 2 hours):**
```bash
# Temporarily set SESSION_LIFETIME=1 in .env
php artisan config:clear
# Wait 1 minute, test expiry
# Then change back to 120
```

---

#### Test 3.3: Logout Clears Session
```
Step 1: Login to application
Step 2: Go to Profile
Step 3: Click Logout
Step 4: Try to go back (browser back button)

Expected Result: ✅ REDIRECTED
- Session deleted from database
- Must login again to access protected pages
```

---

### 4. RATE LIMITING

#### Test 4.1: Login Rate Limiting
```
Step 1: Go to http://localhost:8000/login
Step 2: Try login with wrong password 6 times rapid
Step 3: 6th attempt should fail

Expected Result: ❌ (429 Too Many Requests)
- Error: "Too Many Requests"
- Response status code: 429
- Wait 1 minute, retry: should work ✅
```

**Tools to test:**
```bash
# Bash loop to simulate rapid requests
for i in {1..6}; do
  curl -X POST http://localhost:8000/login \
    -d "email=test@test.com&password=wrong" \
    -L --write-out "\nStatus: %{http_code}\n"
  sleep 0.5
done
```

---

#### Test 4.2: Forgot Password Rate Limiting
```
Step 1: Go to Forgot Password
Step 2: Submit email 4 times rapid
Step 3: 4th attempt should fail

Expected Result: ❌ (429)
- Error after 3 attempts in 1 minute
- Must wait 1 minute to retry
```

---

#### Test 4.3: Activation Rate Limiting
```
Step 1: Get activation link from email
Step 2: Try POST /activate 6 times rapid

Expected Result: ❌ (429)
- Success on attempts 1-5
- Fail on attempt 6 with 429
```

---

#### Test 4.4: Rate Limit Reset After 1 Minute
```
Step 1: Hit rate limit (get 429)
Step 2: Wait exactly 60+ seconds
Step 3: Try again

Expected Result: ✅
- Request succeeds
- Counter reset
```

---

### 5. ACTIVATION TOKEN EXPIRY

#### Test 5.1: Token Works Immediately
```
Setup:
- Admin creates new user
- User receives activation email

Test:
Step 1: Copy activation link from email
Step 2: Click link within 1 minute
Step 3: Fill form and activate

Expected Result: ✅
- Activation succeeds
- User can login with new password
- Token nullified in database
```

---

#### Test 5.2: Token Expires After 24 Hours
```
Setup:
- Admin creates new user
- User receives activation email

Test:
Step 1: Copy activation link
Step 2: Wait 24 hours (or modify DB: `UPDATE users SET activation_token_expires_at = NOW()` for instant test)
Step 3: Try to click link

Expected Result: ❌
- Show error: "Link aktivasi telah kedaluwarsa..."
- User must request new activation link
```

**Quick test (don't wait 24h):**
```bash
# In database, set expiry to past
php artisan tinker
>>> $user = User::find(123);
>>> $user->activation_token_expires_at = now()->subHours(25);
>>> $user->save();

# Now try activation - should fail
```

---

#### Test 5.3: Token Resend Invalidates Old Token
```
Setup:
- Admin creates new user (gets token A)
- User receives email with link A

Test:
Step 1: Copy link A
Step 2: Admin clicks "Resend Activation" → generates token B
Step 3: Try to use link A

Expected Result: ❌ FAILS
- Error: "Link aktivasi tidak valid atau sudah kedaluwarsa"
- Only token B works

Step 4: Use link B
Expected Result: ✅
- Activation succeeds with new token
```

**Verify in database:**
```bash
php artisan tinker
>>> $user = User::where('email', 'test@test.com')->first();
>>> $user->activation_token; # Should show NEW token hash
>>> $user->activation_token_expires_at; # Should be now + 1 day
```

---

#### Test 5.4: Activation Form Shows Expiry Message
```
Step 1: Create user, get activation link
Step 2: Wait 24 hours (or modify as above)
Step 3: Click link

Expected Result: ❌
- Show activation form URL: GET /activate/{token}
- Error message: "Link aktivasi telah kedaluwarsa"
- Form not displayed
- User redirected to login
```

---

## 📊 TEST RESULTS TEMPLATE

```
Date: [Date]
Tester: [Name]
Environment: Development / Staging / Production

PASS/FAIL SUMMARY:
- File Upload Security: PASS / FAIL
- Authorization Checks: PASS / FAIL
- Session Encryption: PASS / FAIL
- Rate Limiting: PASS / FAIL
- Token Expiry: PASS / FAIL

DETAILED RESULTS:

Test 1.1: Valid File Upload
Status: [ ] PASS [ ] FAIL
Notes: 

Test 1.2: Malicious File Rejection
Status: [ ] PASS [ ] FAIL
Notes:

Test 1.3: Path Traversal Prevention
Status: [ ] PASS [ ] FAIL
Notes:

Test 1.4: File Deletion Cleanup
Status: [ ] PASS [ ] FAIL
Notes:

[... continue for all tests ...]

ISSUES FOUND:
1. [Issue description]
2. [Issue description]

APPROVED FOR PRODUCTION: [ ] YES [ ] NO

Tester Signature: _____________ Date: _______
```

---

## 🔧 DEBUGGING TIPS

### If File Upload Fails:
```bash
# Check permissions
ls -la storage/app/private/
chmod 775 storage/app/private

# Check database columns added
php artisan tinker
>>> Schema::getColumnListing('documents')

# Clear config cache
php artisan config:clear
```

### If Authorization Still Allows Access:
```bash
# Verify middleware applied
php artisan route:list | grep approve

# Check supervisor-student relationship
php artisan tinker
>>> $student = Student::find(1);
>>> $student->supervisor_id;
>>> $student->supervisor->user_id;
```

### If Session Not Encrypted:
```bash
# Verify .env setting
cat .env | grep SESSION_ENCRYPT

# Recache config
php artisan config:cache

# Check in database
php artisan tinker
>>> DB::table('sessions')->first();
# payload should be encrypted
```

### If Rate Limiting Not Working:
```bash
# Verify middleware applied
php artisan route:list | grep login

# Check throttle config
php artisan tinker
>>> config('app.throttle')

# Clear route cache
php artisan route:clear
```

---

## ✅ ACCEPTANCE CRITERIA

**Phase 1 is APPROVED for production when:**

- [ ] All 5 test categories: 100% PASS
- [ ] No security warnings in tests
- [ ] No data loss or corruption
- [ ] Performance not degraded
- [ ] Migrations run cleanly
- [ ] No errors in application logs
- [ ] QA sign-off received

---

**Testing Started:** [Date/Time]  
**Testing Completed:** [Date/Time]  
**QA Sign-off:** _____________  
**Date:** _____________

