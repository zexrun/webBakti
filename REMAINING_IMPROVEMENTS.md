# 📋 REMAINING IMPROVEMENTS & MISSING FEATURES

**Date:** 17 August 2026  
**Analysis:** Post-Sprint Code Review  
**Status:** Identified for Phase 4

---

## 🔍 FINDINGS SUMMARY

While core security, performance, and feature completeness (CRUD) have been addressed in Phases 1-3, there are still several improvements and missing features that should be tackled for a production-ready system.

**Total Items Identified:** 14  
**Priority Breakdown:** 4 HIGH, 6 MEDIUM, 4 LOW

---

## 🟠 HIGH PRIORITY IMPROVEMENTS

### 1. **Duplicate/Dead Code in Controllers**
**Files:** 
- `app/Http/Controllers/Admin/MonitoringController.php` (lines 14-37)
- `app/Http/Controllers/Supervisor/SupervisorController.php` (lines 79-106)

**Issues:**
- `index2()` and `showStudent2()` methods not used
- Commented-out code cluttering codebase
- Dead methods consuming space

**Impact:** Code maintainability, confusion for future developers

**Fix:** 
```php
// REMOVE: showStudent2(), index2(), myStudents(), viewStudent()
// KEEP: index(), showSupervisor(), showStudent(), showDocuments()
```

**Effort:** 30 minutes  
**Priority:** HIGH (code cleanliness)

---

### 2. **Logic Error in Supervisor TaskController**
**File:** `app/Http/Controllers/Supervisor/TaskController.php` (line 54-56)

**Issue:**
```php
if ($request->hasFile('file')) {
    // ...
} else if ($request->hasFile('file')) {  // ❌ DUPLICATE CONDITION
    dd('File not valid');  // ❌ UNREACHABLE CODE
}
```

**Impact:** Dead code, confusing logic flow

**Fix:**
```php
// Remove else if block entirely - it's unreachable
```

**Effort:** 5 minutes  
**Priority:** HIGH (logic error)

---

### 3. **Missing Authorization Check in Supervisor TaskController**
**File:** `app/Http/Controllers/Supervisor/TaskController.php` (line 83-91)

**Issues:**
```php
public function show(Task $task)
{
    if (Auth::user() === 'admin') {  // ❌ WRONG COMPARISON (object vs string)
        return view(...);
    }
    // No authorization check that supervisor owns this task!
}
```

**Problems:**
- Supervisor can view ANY task (not just theirs)
- Wrong comparison (Auth::user() is object, not string)
- No IDOR protection

**Fix:**
```php
public function show(Task $task)
{
    if (Auth::user()->role !== 'supervisor' || 
        $task->supervisor_id !== Auth::user()->supervisor->id) {
        abort(403, 'AKSES DITOLAK');
    }
    // ... rest of code
}
```

**Effort:** 15 minutes  
**Priority:** HIGH (security - IDOR)

---

### 4. **Missing Task Edit/Update/Delete Methods**
**File:** `app/Http/Controllers/Supervisor/TaskController.php`

**Issue:**
- No `edit()` method
- No `update()` method
- No `destroy()` method
- Supervisors cannot modify/delete tasks after creation

**Impact:** Feature incomplete, prevents task management corrections

**Implementation Needed:**
```php
public function edit(Task $task) { ... }
public function update(Request $request, Task $task) { ... }
public function destroy(Task $task) { ... }
```

**Effort:** 1-2 hours  
**Priority:** HIGH (feature completeness)

---

## 🟡 MEDIUM PRIORITY IMPROVEMENTS

### 5. **Missing Search/Filter on Monitoring Page**
**File:** `app/Http/Controllers/Admin/MonitoringController.php`

**Issue:**
- Can list all students/supervisors
- Cannot search by name
- Cannot filter by status
- No pagination on large datasets

**Feature Gap:** Usability issue with 100+ students

**Implementation:**
```php
public function index(Request $request)
{
    $search = $request->input('search');
    $supervisors = Supervisor::with(['user', 'students.user'])
        ->when($search, fn($q) => $q->whereHas('user', fn($u) => 
            $u->where('name', 'like', "%$search%")
        ))
        ->paginate(20);
    // ...
}
```

**Effort:** 1 hour  
**Priority:** MEDIUM (usability)

---

### 6. **Missing Validation on Task Due Date**
**File:** `app/Http/Controllers/Supervisor/TaskController.php` (line 40-49)

**Issue:**
```php
'due_date' => 'nullable|date',  // ❌ Allows past dates
```

**Problem:** Can set deadline to past date, confusing for students

**Fix:**
```php
'due_date' => 'nullable|date|after_or_equal:today',
```

**Effort:** 15 minutes  
**Priority:** MEDIUM (data validation)

---

### 7. **Missing Authorization in Task Grade Method**
**File:** `app/Http/Controllers/Supervisor/TaskController.php` (line 106-121)

**Issue:**
```php
public function grade(Request $request, Submission $submission)
{
    // No check if supervisor owns this submission's task!
    $submission->update([...]);
}
```

**Problem:** Supervisor can grade ANY submission from ANY task

**Fix:**
```php
public function grade(Request $request, Submission $submission)
{
    if ($submission->task->supervisor_id !== Auth::user()->supervisor->id) {
        abort(403, 'AKSES DITOLAK');
    }
    // ... rest of code
}
```

**Effort:** 15 minutes  
**Priority:** MEDIUM (IDOR security)

---

### 8. **Missing Notification on Task Grade**
**File:** `app/Http/Controllers/Supervisor/TaskController.php`

**Issue:**
- Supervisor grades submission
- No notification sent to student

**Impact:** Students don't know grades are ready

**Implementation:**
```php
public function grade(Request $request, Submission $submission)
{
    // ... validation & update ...
    
    $submission->student->user->notify(new TaskGraded($submission));
    
    return redirect()->with('success', ...');
}
```

**Effort:** 30 minutes  
**Priority:** MEDIUM (UX/notifications)

---

### 9. **Missing File Cleanup on Task Delete**
**File:** `app/Http/Controllers/Supervisor/TaskController.php`

**Issue:**
- No `destroy()` method exists
- When task deleted, attachment file not cleaned up
- Orphaned files accumulate in storage

**Implementation:** Add to Task model:
```php
protected static function boot()
{
    parent::boot();
    static::deleting(function ($task) {
        if ($task->file_path && Storage::disk('public')->exists($task->file_path)) {
            Storage::disk('public')->delete($task->file_path);
        }
    });
}
```

**Effort:** 30 minutes  
**Priority:** MEDIUM (file management)

---

### 10. **Missing Student Profile Editing**
**File:** `app/Http/Controllers/Student/ProfileController.php`

**Issue:**
- StudentProfileController has `edit()` method
- Need to verify what fields can be edited
- Missing validation for NIM, university, semester

**Check:** `app/Http/Controllers/Student/ProfileController.php`

**Effort:** 1-2 hours  
**Priority:** MEDIUM (feature completeness)

---

## 🟢 MEDIUM-LOW PRIORITY

### 11. **No Attendance Status Export (CSV/PDF)**
**File:** `app/Http/Controllers/Admin/AttendanceController.php`

**Feature Gap:**
- Can view attendance data in table
- Cannot export to CSV or PDF for reporting
- Admin must copy-paste data

**Implementation:** Add `export()` method with CSV generation

**Effort:** 2-3 hours  
**Priority:** MEDIUM-LOW (reporting)

---

### 12. **No Attendance Exception Types**
**File:** `app/Http/Controllers/AttendanceController.php`

**Issue:**
- Students can request "exception"
- No reason/type specified
- No distinction between sick, permission, etc.

**Enhancement:**
```php
'exception_type' => 'required|in:sick,permission,emergency,other',
'reason' => 'required|string|max:500',
```

**Effort:** 1-2 hours  
**Priority:** MEDIUM-LOW (UX enhancement)

---

### 13. **No Batch Operations (Admin)**
**Issue:**
- Admin can only approve 1 attendance at a time
- No bulk approval for multiple students

**Enhancement:** Add checkbox + bulk approve button

**Effort:** 2-3 hours  
**Priority:** LOW (admin convenience)

---

### 14. **No Dashboard Statistics for Supervisors**
**File:** `app/Http/Controllers/Supervisor/SupervisorController.php`

**Issue:**
```php
public function dashboard()
{
    return view('supervisor.dashboard');  // ❌ No data passed
}
```

**Missing Dashboard Data:**
- Number of active students
- Pending submissions to grade
- Tasks near deadline
- Overall attendance stats

**Implementation:**
```php
public function dashboard()
{
    $supervisor = Auth::user()->supervisor;
    
    $stats = [
        'active_students' => $supervisor->students()->count(),
        'pending_submissions' => Submission::whereHas('task', 
            fn($q) => $q->where('supervisor_id', $supervisor->id))
            ->whereNull('grade')->count(),
        'tasks_due_soon' => Task::where('supervisor_id', $supervisor->id)
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->count(),
    ];
    
    return view('supervisor.dashboard', $stats);
}
```

**Effort:** 2-3 hours  
**Priority:** LOW (UX enhancement)

---

## 📊 SUMMARY TABLE

| Item | Type | Priority | Effort | Status |
|------|------|----------|--------|--------|
| Duplicate code in MonitoringController | Code Quality | HIGH | 30m | ❌ TODO |
| Logic error in TaskController | Bug | HIGH | 5m | ❌ TODO |
| Missing auth in Task.show() | Security | HIGH | 15m | ❌ TODO |
| Missing Task edit/update/delete | Feature | HIGH | 2h | ❌ TODO |
| Missing search on Monitoring | UX | MEDIUM | 1h | ❌ TODO |
| Task due_date validation | Validation | MEDIUM | 15m | ❌ TODO |
| Missing auth in Task.grade() | Security | MEDIUM | 15m | ❌ TODO |
| Missing grade notification | Feature | MEDIUM | 30m | ❌ TODO |
| Missing file cleanup on task delete | Cleanup | MEDIUM | 30m | ❌ TODO |
| Student profile completeness check | Feature | MEDIUM | 1-2h | ❌ TODO |
| Attendance export (CSV/PDF) | Feature | LOW | 2-3h | ❌ TODO |
| Attendance exception types | UX | LOW | 1-2h | ❌ TODO |
| Batch operations | Admin | LOW | 2-3h | ❌ TODO |
| Supervisor dashboard stats | UX | LOW | 2-3h | ❌ TODO |

---

## 🎯 RECOMMENDED NEXT PHASE

### **Phase 4: CODE CLEANUP & HIGH-PRIORITY FIXES (1-2 days)**

**Must Do (Quick wins):**
1. ✅ Remove dead code from controllers (30m)
2. ✅ Fix logic error in TaskController (5m)
3. ✅ Add authorization to Task.show() (15m)
4. ✅ Add authorization to Task.grade() (15m)
5. ✅ Fix due_date validation (15m)

**Total:** ~1.5 hours

**Should Do (Feature Completeness):**
1. ✅ Add Task edit/update/delete methods (2h)
2. ✅ Add file cleanup on task delete (30m)
3. ✅ Add grade notification (30m)

**Total:** ~3 hours

---

## 📝 IMPLEMENTATION ROADMAP

### **Week 1 (Current)**
- [x] Phase 1-3: Security, Performance, Completeness
- [ ] Phase 4: Code cleanup + HIGH priority fixes

### **Week 2**
- [ ] Additional features (search, dashboard)
- [ ] QA testing full suite
- [ ] Production deployment

### **Week 3+**
- [ ] UI improvements
- [ ] Additional reporting features
- [ ] Performance monitoring

---

## 🔗 RELATED FILES TO CHECK

```
✓ app/Http/Controllers/Supervisor/TaskController.php - MULTIPLE ISSUES
✓ app/Http/Controllers/Supervisor/SupervisorController.php - Dead code + missing features
✓ app/Http/Controllers/Admin/MonitoringController.php - Dead code
✓ app/Http/Controllers/Student/ProfileController.php - Verify completeness
✓ app/Models/Task.php - Add file cleanup event
```

---

## 💡 QUICK WINS (Can do in 2-3 hours)

```bash
# 1. Remove dead code
# 2. Fix TaskController logic error
# 3. Add missing authorization checks (2 places)
# 4. Add due_date validation
# 5. Add file cleanup to Task model
```

**Total Impact:** Fixes 5 HIGH priority issues in ~2-3 hours  
**ROI:** High (security fixes + code quality)

---

**Recommendation:** Include Phase 4 in this sprint before QA testing to ensure code quality is top-notch.

