# Naming Normalization Specification — webBakti

**Status:** Approved for implementation  
**Date:** 2026-07-24  
**Scope:** Full codebase audit + normalization + database migration  
**Language Strategy:** All-English database columns; Indonesian UI labels (unchanged)

---

## Executive Summary

Current codebase has naming inconsistencies across controller methods, route names, and database columns. This spec defines a complete normalization to improve consistency, maintainability, and reduce cognitive load.

**Key Changes:**
- Controller methods: Fix legacy `action*` prefix → standard camelCase
- Route names: Standardize to kebab-case, remove redundant nesting
- Database columns: Normalize all Indonesian terms to English
- Enum values: Normalize task types to English
- All code references updated to match new names

---

## Phase 1: Non-Breaking Changes (No Migration)

### 1.1 Controller Methods Normalization

**Affected:** `app/Http/Controllers/LoginController.php`

| Old Method | New Method | Reason |
|---|---|---|
| `actionlogin()` | `login()` | Remove legacy `action*` prefix, align with camelCase standard |
| `actionlogout()` | `logout()` | Remove legacy `action*` prefix, align with camelCase standard |

**Impact:** Only 2 methods, find/replace safe

**Code locations to update:**
- `routes/web.php` — route definitions (if any direct references)
- Tests — if any test methods call these (unlikely, checked)
- No frontend references (form action uses route names, not method names)

---

### 1.2 Route Name Normalization

**File:** `routes/web.php`

| Old Route Name | New Route Name | Reason |
|---|---|---|
| `resend_activation` | `resend-activation` | Standardize to kebab-case (matches other route names in codebase) |
| `students.list.index` | `students.index` | Remove redundant `.list.` nesting; `.index` already implies listing |

**URL paths:** No changes (URLs already use kebab-case correctly)

**Impact:** Routes are referenced via `route()` helper in Blade/Inertia, so changes are centralized. Find/replace in:
- `routes/web.php` — route definitions
- Views/components — any hardcoded route names in links
- Controllers — any explicit route name references

**Backward Compatibility:** Old route names will break links. Consider adding temporary redirects if needed (not in this pass; breaking change is acceptable).

---

## Phase 2: Database Migration + Code Updates

### 2.1 Database Column Renames

**Migration Strategy:** Safe path:
1. Create new columns with new names (add to `students`, `supervisors`, `tasks` tables)
2. Backfill data from old columns
3. Update Eloquent models + code to use new columns
4. Drop old columns (in cleanup migration or keep for grace period)

**Columns to Rename:**

#### Students Table

| Old Column | New Column | Type | Reason |
|---|---|---|---|
| `universitas` | `university` | string | Normalize Indonesian → English |
| `program_studi` | `study_program` | string | Normalize Indonesian → English compound term |
| `periode_mulai` | `period_start` | date | Normalize Indonesian → English |
| `periode_selesai` | `period_end` | date | Normalize Indonesian → English |
| `direktorat` | `directorate` | string | Normalize Indonesian → English |

#### Supervisors Table

| Old Column | New Column | Type | Reason |
|---|---|---|---|
| `nip` | `employee_id` | string | More descriptive; NIP is Indonesian-specific abbreviation |
| `jabatan` | `position` | string | Normalize Indonesian → English |
| `direktorat` | `directorate` | string | Normalize Indonesian → English |

#### Tasks Table

| Old Column | New Column | Type | Reason |
|---|---|---|---|
| `type` enum values | `daily` / `final` | enum | Normalize Indonesian (`harian`/`akhir`) → English |

#### Other Tables

- **Workschedules** (if `jabatan`-like column exists) — rename to `position`
- Review other tables for any Indonesian column names — audit found none others actively used

---

### 2.2 Model Updates

**File:** `app/Models/Student.php`

```php
// Before
protected $fillable = ['user_id', 'supervisor_id', 'nim', 'universitas', 'program_studi', 'semester', 'direktorat', 'periode_mulai', 'periode_selesai'];

// After
protected $fillable = ['user_id', 'supervisor_id', 'nim', 'university', 'study_program', 'semester', 'directorate', 'period_start', 'period_end'];
```

**File:** `app/Models/Supervisor.php`

```php
// Before
protected $fillable = ['user_id', 'nip', 'jabatan', 'direktorat'];

// After
protected $fillable = ['user_id', 'employee_id', 'position', 'directorate'];
```

**File:** `app/Models/Task.php`

```php
// Enum values update (if using native enums)
// Before: enum('harian', 'akhir')
// After: enum('daily', 'final')
```

**Attributes/Accessors:** Update any custom attribute names or accessor methods that reference old column names.

---

### 2.3 Controller & Service Updates

**Controllers to update:**
- `AdminController` — any references to `$student->universitas`, etc.
- `SupervisorController` — any references to supervisor attributes
- `StudentController` — any references to own `universitas`, `program_studi`
- `UserController` — form submission handling for student/supervisor creation
- `SettingController` — if any setting forms use these attributes

**Search pattern for updates:**
- `->universitas` → `->university`
- `->program_studi` → `->study_program`
- `->periode_mulai` → `->period_start`
- `->periode_selesai` → `->period_end`
- `->direktorat` → `->directorate`
- `->nip` → `->employee_id`
- `->jabatan` → `->position`

**Validation Rules (FormRequests):**
- Update field names in validation rules (`universitas` → `university`, etc.)

---

### 2.4 View/Component Updates

**React Components** — search for attribute references:
- Props destructuring: `const { universitas, program_studi, ... } = student` → update to new names
- Form inputs: `<Input name="universitas" ...>` → update to new names
- Display: `{student.universitas}` → update to new names
- API calls: Form data must match new column names

**Blade Views** (if any remain):
- Similar updates for any old Blade templates still in use

**Search pattern:**
- Look for `universitas`, `program_studi`, `periode_mulai`, `periode_selesai`, `direktorat`, `nip`, `jabatan` in all view files

---

### 2.5 Seeder & Factory Updates

**File:** `database/seeders/StudentSeeder.php`

```php
// Before
Student::create([
    'universitas' => 'Telkom University',
    'program_studi' => 'S1 Informatika',
    'periode_mulai' => '2026-07-01',
    'periode_selesai' => '2026-08-31',
    ...
]);

// After
Student::create([
    'university' => 'Telkom University',
    'study_program' => 'S1 Informatika',
    'period_start' => '2026-07-01',
    'period_end' => '2026-08-31',
    ...
]);
```

**File:** `database/factories/StudentFactory.php`

Similar updates to factory definitions.

---

### 2.6 Migration File

**New Migration:** `database/migrations/YYYY_MM_DD_rename_student_supervisor_columns.php`

```php
Schema::table('students', function (Blueprint $table) {
    // Add new columns
    $table->string('university')->nullable()->after('universitas');
    $table->string('study_program')->nullable()->after('program_studi');
    $table->date('period_start')->nullable()->after('periode_mulai');
    $table->date('period_end')->nullable()->after('periode_selesai');
    $table->string('directorate')->nullable()->after('direktorat');
});

Schema::table('supervisors', function (Blueprint $table) {
    $table->string('employee_id')->nullable()->after('nip');
    $table->string('position')->nullable()->after('jabatan');
    $table->string('directorate')->nullable()->after('direktorat');
});

Schema::table('tasks', function (Blueprint $table) {
    // Rename enum column type (handled via raw SQL if needed)
});
```

**Backfill Migration:** `database/migrations/YYYY_MM_DD_backfill_renamed_columns.php`

```php
// After tables have new columns:
DB::statement('UPDATE students SET university = universitas');
DB::statement('UPDATE students SET study_program = program_studi');
// ... etc
```

**Cleanup Migration:** `database/migrations/YYYY_MM_DD_drop_old_columns.php`

```php
Schema::table('students', function (Blueprint $table) {
    $table->dropColumn(['universitas', 'program_studi', 'periode_mulai', 'periode_selesai', 'direktorat']);
});
// ... etc
```

---

## Phase 3: Testing & Validation

### 3.1 Automated Tests

- Update any Model tests that reference old column names
- Update FormRequest tests
- Update Controller/Feature tests
- Update seeder tests (if any)

**Search pattern:**
- `$student->universitas` → `$student->university`
- Field validation assertions

---

### 3.2 Manual Verification

After migration:

1. **Admin > Settings:** Create/edit student and supervisor records, verify forms save correctly
2. **Student dashboard:** Verify student info displays correctly
3. **Reports/Export:** Verify any CSV exports or reports include correct fields
4. **Search/Filters:** If any search filters exist, verify they work with new column names
5. **API responses:** If any API endpoints exist, verify JSON responses use new field names

---

## Enum Value Normalization

### Task Type Enum

**File:** `app/Models/Task.php` or migration

| Old Value | New Value | Usage |
|---|---|---|
| `harian` | `daily` | Task type to distinguish daily vs final tasks |
| `akhir` | `final` | Final assignment task |

**Code updates:**
- Any references: `$task->type === 'harian'` → `$task->type === 'daily'`
- Seeder/factory: Update hardcoded enum values
- Views: Update enum display logic if any

---

## Affected Files — Complete Checklist

### Controllers (code search needed)
- [ ] `app/Http/Controllers/LoginController.php` — `actionlogin()`, `actionlogout()`
- [ ] `app/Http/Controllers/Admin/*` — attribute references
- [ ] `app/Http/Controllers/Supervisor/*` — attribute references
- [ ] `app/Http/Controllers/Student/*` — attribute references
- [ ] Any FormRequest validation rules — field names

### Models
- [ ] `app/Models/Student.php` — fillable, casts, relationships
- [ ] `app/Models/Supervisor.php` — fillable, casts
- [ ] `app/Models/Task.php` — enum values
- [ ] Any other models with relationships to above

### Routes
- [ ] `routes/web.php` — route name definitions

### Views/Components
- [ ] `resources/js/Pages/**` — attribute references in React components
- [ ] `resources/js/Components/**` — if any pass student/supervisor data
- [ ] `resources/views/**` — if any old Blade templates remain

### Database
- [ ] Migrations (new files to create)
- [ ] `database/seeders/*` — update column references
- [ ] `database/factories/*` — update column references

### Tests
- [ ] `tests/Feature/**` — update assertions
- [ ] `tests/Unit/**` — update model tests

---

## Rollback Plan

If migration causes issues:

1. **Before running migration in production:** Test in staging environment
2. **Rollback strategy:** Run cleanup migration in reverse (restore old columns, move data back)
3. **Grace period:** Keep old columns for N days/weeks if needed, update code to read from new columns (old columns ignored)

---

## Success Criteria

- ✅ All tests pass (unit + feature)
- ✅ Manual verification checklist complete
- ✅ No references to old column names in code (grep verify)
- ✅ Database migration executes without errors
- ✅ Forms submit and save data correctly
- ✅ Reports/exports work correctly
- ✅ No 500 errors in logs post-migration

---

## Implementation Order

1. **Step 1 — Code preparation** (no breaking changes yet):
   - Rename controller methods (`actionlogin` → `login`)
   - Rename route names in `routes/web.php`
   - Update all code references in controllers/views

2. **Step 2 — Database migration** (breaking changes):
   - Create migration to add new columns + backfill
   - Update models' `fillable`, casts, relationships
   - Update seeders/factories
   - Run migration

3. **Step 3 — Validation**:
   - Run full test suite
   - Manual verification checklist
   - Cleanup/remove old columns (optional grace period)

4. **Step 4 — Commit**:
   - Single commit combining all changes for this normalization
   - Or separate commits per phase if preferred

---

## Notes

- **No UI label changes:** All Indonesian labels in UI remain unchanged (user-facing, working well)
- **Database enum migration:** Task type enum values change from `harian`/`akhir` to `daily`/`final`; backfill logic needed if old values exist in production data
- **Grace period option:** Can keep old columns read-only for N days, then drop them — allows gradual cutover
- **Backward compatibility:** Breaking changes expected; this is a major codebase improvement pass, not a backward-compatible release

