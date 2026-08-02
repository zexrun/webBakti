# Server-Side Face Verification Implementation

## Overview

This document describes the server-side face verification system for attendance check-in, which provides security against client-side manipulation and spoofing attempts.

## Architecture

### Before: Client-Side Only (Vulnerable)
```
Browser                          Server
  ↓
Extract face descriptor    →    Accept without validation
Compare with stored      →    Save immediately
Send approval/rejection   →    Trust client decision
```

**Risks:**
- Browser developer tools can manipulate face_descriptor
- Response interception can change result
- No server-side validation of actual photo

### After: Server-Side Verification (Secure)
```
Browser                          Server
  ↓
Capture photo + location   →    Extract descriptor server-side
Send to server                  Compare with stored descriptor
                          →    Verify geofencing
                          →    Validate location GPS
                          →    Save decision with audit trail
```

**Security Benefits:**
- Photo validated on trusted server
- Descriptors extracted consistently
- Geofencing verified server-side
- Complete audit trail
- Client cannot manipulate results

---

## Components

### 1. ServerFaceVerificationService (`app/Services/ServerFaceVerificationService.php`)

Main service for face verification logic.

#### Key Methods:

**`extractFaceDescriptor(string $imageBase64): ?array`**
- Converts base64 image to file
- Calls Node.js script to extract face descriptor
- Returns array of 128 floating-point values representing face
- Returns null if face not detected

**`compareFaceDescriptors(array $descriptor1, array $descriptor2, float $threshold = 0.6): array`**
- Calculates Euclidean distance between two descriptors
- Returns match result and distance metric
- Distance < 0.6 typically indicates same person
- Threshold tunable based on accuracy needs

**`verifyAttendance(string $photoBase64, array $currentLocation, array $geofenceCenter, float $maxDistance = 50): array`**
- Master verification method
- Validates photo + location together
- Returns success/failure with detailed reason
- All-in-one method for attendance verification

**`getOfficeLocation(): array`**
- Returns configured office coordinates
- Defaults to BAKTI office location
- Can be overridden via config

### 2. Node.js Descriptor Extractor (`resources/face-verification/extract-descriptor.cjs`)

Runs on server to extract face descriptors safely.

```bash
node extract-descriptor.cjs <image-path> <output-path>
```

- Uses @vladmandic/face-api (TensorFlow.js)
- Loads image via canvas
- Detects single face with landmarks
- Extracts 128-dimensional descriptor
- Outputs JSON array to file

**Dependencies:**
- `@vladmandic/face-api`
- `canvas`
- `@tensorflow/tfjs`

### 3. Updated AttendanceController (`app/Http/Controllers/AttendanceController.php`)

Enhanced `checkIn()` method now:
1. Validates photo exists
2. Converts photo to base64
3. Calls ServerFaceVerificationService
4. Falls back to client-side if server-side fails
5. Stores verification status in database

### 4. Database Schema

Migration: `2026_07_27_135027_add_face_verification_to_attendances.php`

New columns on `attendances` table:
- `face_distance` (float) - Distance metric from face comparison (0-1 scale)
- `location_distance` (float) - Distance from office in meters
- `verification_status` (enum) - One of: `auto_verified`, `manual_verified`, `rejected`, `pending`
- `verified_at` (timestamp) - When verification completed
- `verification_notes` (text) - Notes/error messages from verification

---

## Usage

### Installation

```bash
npm install @vladmandic/face-api canvas
php artisan migrate
```

### Verification Flow

When student attempts check-in:

```php
$verification = ServerFaceVerificationService::verifyAttendance(
    $photoBase64,        // base64-encoded photo
    [
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ],
    [                   // Office location
        'latitude' => -6.2088,
        'longitude' => 106.8057,
    ],
    50                  // Max distance in meters
);

if ($verification['success']) {
    // Auto-approve attendance
    $attendance->verification_status = 'auto_verified';
    $attendance->verified_at = $verification['verified_at'];
} else {
    // Return error with reason
    return response()->json([
        'success' => false,
        'message' => $verification['reason'],
        'code' => $verification['code']
    ], 403);
}
```

### Response Codes

Success case:
```json
{
    "success": true,
    "face_distance": 0.453,
    "location_distance": 25,
    "verified_at": "2026-07-27T14:30:00Z"
}
```

Failure cases:
- `FACE_NOT_DETECTED` - No face in photo
- `NO_STORED_FACE` - Student hasn't registered profile photo
- `FACE_MISMATCH` - Face doesn't match stored descriptor
- `LOCATION_OUT_OF_RANGE` - Too far from office

---

## Configuration

### Environment Variables (Optional)

Add to `.env` if not using defaults:
```env
FACE_VERIFICATION_THRESHOLD=0.6
OFFICE_LATITUDE=-6.2088
OFFICE_LONGITUDE=106.8057
GEOFENCE_RADIUS_METERS=50
```

### In Code

Customize thresholds:
```php
// Stricter face matching (0.5)
ServerFaceVerificationService::compareFaceDescriptors(
    $desc1, $desc2, 0.5
);

// Stricter geofencing (30m instead of 50m)
ServerFaceVerificationService::verifyAttendance(
    $photo, $location, $office, 30
);
```

---

## Testing

### Run Unit Tests

```bash
php artisan test tests/Feature/ServerFaceVerificationTest.php
```

### Manual Testing

1. **Test descriptor comparison:**
   - Same face → distance < 0.6 → match ✓
   - Different faces → distance > 0.6 → no match ✓

2. **Test geofencing:**
   - At office → distance < 50m → pass ✓
   - Away from office → distance > 50m → fail ✓

3. **Test attendance flow:**
   - Good photo + at office → auto-verified ✓
   - Bad photo → face mismatch error ✓
   - Wrong location → location error ✓

---

## Performance Considerations

### Face Extraction
- ~2-3 seconds per photo (CPU-bound)
- Uses Node.js subprocess (not blocking)
- Consider timeout for slow servers

### Geofencing
- Haversine calculation: < 1ms
- No database queries needed
- Very fast

### Overall
- Adds ~2-3 seconds to check-in time
- Not suitable for high-volume concurrent check-ins
- OK for typical internship program (50-100 students)

---

## Security Notes

### What's Protected
✅ Face descriptor extraction (server-side only)  
✅ Face matching logic (server validates)  
✅ Geofencing validation (server validates)  
✅ Audit trail (all results logged)  

### What's Still Needed
⚠️ HTTPS only (prevent man-in-middle)  
⚠️ Rate limiting on check-in endpoint  
⚠️ Photo quality validation  
⚠️ Liveness detection (optional - detect if photo is replay/fake)  

### Not Protected Against
❌ Compromised server (if attacker has DB access, can modify verification_status)  
❌ Extremely high-quality fake faces (deepfakes) - but higher threshold helps  
❌ Insider threat (admin manually overriding)  

---

## Troubleshooting

### "Face extraction failed"
- Check Node.js available in PATH: `which node`
- Check canvas dependencies installed: `npm ls canvas`
- Check photo format is valid JPEG/PNG
- Try with different photo

### "Face distance is null"
- face_descriptor might not be extracted
- Check Node script output for errors
- Verify TensorFlow model files present

### "No stored face"
- Student must register profile photo first
- Check `users.face_descriptor` is populated
- Ensure profile photo has clear face

### Geofencing always fails
- Check office coordinates correct
- Check GPS accuracy ± 10 meters
- Try increasing max_distance parameter

---

## Future Improvements

1. **Liveness Detection**
   - Detect if photo is a replay/fake
   - Ask for head movement during capture
   
2. **Photo Quality Validation**
   - Ensure face is clear and well-lit
   - Reject blurry/low-contrast photos
   
3. **Batch Optimization**
   - Pre-warm TensorFlow models
   - Cache descriptors for better performance
   
4. **Gradual Rollout**
   - Start with auto_verified suggestions
   - Admin reviews all results
   - Gradual transition to auto-approval

---

## Files Modified

- `app/Http/Controllers/AttendanceController.php` - Added server verification
- `database/migrations/2026_07_27_*` - Added verification columns
- `app/Services/ServerFaceVerificationService.php` - New service
- `resources/face-verification/extract-descriptor.cjs` - New Node script
- `tests/Feature/ServerFaceVerificationTest.php` - Unit tests
- `package.json` - Added face-api.js & canvas dependencies

---

## References

- [@vladmandic/face-api](https://github.com/vladmandic/face-api)
- [Canvas NPM Package](https://www.npmjs.com/package/canvas)
- [TensorFlow.js](https://www.tensorflow.org/js)
- [Haversine Formula](https://en.wikipedia.org/wiki/Haversine_formula)
