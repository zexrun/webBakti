# Naming Normalization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Normalize all database column names from Indonesian to English, standardize controller method names and route names across the entire codebase, ensuring consistency and maintainability.

**Architecture:** Three-phase approach: (1) Code-level changes to controller methods and route names (no breaking changes to database), (2) Database migration with backfill and model updates, (3) Comprehensive validation and cleanup.

**Tech Stack:** Laravel 12 migrations, Eloquent models, React components, PHP controllers, bash for find/replace verification.

---

### Task 1: Rename Controller Methods (LoginController)

**Files:**
- Modify: `app/Http/Controllers/LoginController.php`
- Modify: `routes/web.php`
- Test: Integration tests if they reference these methods (unlikely)

- [ ] **Step 1: Read LoginController to understand current implementation**

Run: `cat app/Http/Controllers/LoginController.php`

Note: You'll see `actionlogin()` and `actionlogout()` methods (legacy pattern).

- [ ] **Step 2: Rename actionlogin() to login() in LoginController**

In `app/Http/Controllers/LoginController.php`, find:
```php
public function actionlogin()
```

Replace with:
```php
public function login()
```

And update any `$this->redirect()` or internal calls if they exist within the method body.

- [ ] **Step 3: Rename actionlogout() to logout() in LoginController**

In `app/Http/Controllers/LoginController.php`, find:
```php
public function actionlogout()
```

Replace with:
```php
public function logout()
```

- [ ] **Step 4: Verify no internal references to old method names**

Run: `grep -r "actionlogin\|actionlogout" app/ resources/ --include="*.php" --include="*.jsx"`

Expected: No results (if any found, update those references).

- [ ] **Step 5: Verify LoginController syntax**

Run: `php -l app/Http/Controllers/LoginController.php`

Expected: `No syntax errors detected`

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/LoginController.php
git commit -m "refactor: Rename LoginController legacy action* methods to standard camelCase"
```

---

### Task 2: Standardize Route Names (routes/web.php)

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Read routes/web.php to find route name definitions**

Run: `grep -n "->name(" routes/web.php | grep -E "resend_activation|students\.list"`

Note: You'll find these route name definitions.

- [ ] **Step 2: Rename route name resend_activation to resend-activation**

In `routes/web.php`, find:
```php
->name('resend_activation')
```

Replace with:
```php
->name('resend-activation')
```

- [ ] **Step 3: Rename route name students.list.index to students.index**

In `routes/web.php`, find:
```php
->name('students.list.index')
```

Replace with:
```php
->name('students.index')
```

- [ ] **Step 4: Update any code references to old route names**

Run: `grep -r "resend_activation\|students\.list\.index" app/ resources/ --include="*.php" --include="*.jsx" | grep -v "routes/web.php"`

For each match found:
- If it's in a controller or Blade: `route('resend-activation')` or `route('students.index')`
- If it's in React: `window.route('resend-activation')` or `window.route('students.index')`

Update all occurrences.

- [ ] **Step 5: Verify routes syntax**

Run: `php artisan route:list | grep -E "resend-activation|students\.index"`

Expected: Both new route names appear in the list.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php app/ resources/
git commit -m "refactor: Standardize route names to kebab-case and remove redundant route nesting"
```

---

### Task 3: Create Database Migration (Add New Columns)

**Files:**
- Create: `database/migrations/YYYY_MM_DD_add_normalized_columns.php`

- [ ] **Step 1: Create migration file**

Run: `php artisan make:migration add_normalized_columns --create=false`

This creates a new migration file in `database/migrations/`.

- [ ] **Step 2: Write the migration to add new columns**

Open the generated migration file and replace its `up()` and `down()` methods:

```php
public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
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
}

public function down(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn(['university', 'study_program', 'period_start', 'period_end', 'directorate']);
    });

    Schema::table('supervisors', function (Blueprint $table) {
        $table->dropColumn(['employee_id', 'position', 'directorate']);
    });
}
```

- [ ] **Step 3: Run the migration**

Run: `php artisan migrate`

Expected: Migration succeeds, new columns added to both tables.

- [ ] **Step 4: Verify columns exist**

Run: `php artisan tinker --execute="echo json_encode(\\DB::getSchemaBuilder()->getColumnListing('students'), JSON_PRETTY_PRINT);"`

Expected: New columns (`university`, `study_program`, `period_start`, `period_end`, `directorate`) appear in the list alongside old ones.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/
git commit -m "chore: Create migration to add normalized database columns"
```

---

### Task 4: Create Backfill Migration (Copy Old Data to New Columns)

**Files:**
- Create: `database/migrations/YYYY_MM_DD_backfill_normalized_columns.php`

- [ ] **Step 1: Create another migration for backfill**

Run: `php artisan make:migration backfill_normalized_columns --create=false`

- [ ] **Step 2: Write backfill logic**

```php
public function up(): void
{
    DB::statement('UPDATE students SET university = universitas WHERE university IS NULL');
    DB::statement('UPDATE students SET study_program = program_studi WHERE study_program IS NULL');
    DB::statement('UPDATE students SET period_start = periode_mulai WHERE period_start IS NULL');
    DB::statement('UPDATE students SET period_end = periode_selesai WHERE period_end IS NULL');
    DB::statement('UPDATE students SET directorate = direktorat WHERE directorate IS NULL');

    DB::statement('UPDATE supervisors SET employee_id = nip WHERE employee_id IS NULL');
    DB::statement('UPDATE supervisors SET position = jabatan WHERE position IS NULL');
    DB::statement('UPDATE supervisors SET directorate = direktorat WHERE directorate IS NULL');
}

public function down(): void
{
    // Set new columns back to NULL
    DB::statement('UPDATE students SET university = NULL');
    DB::statement('UPDATE students SET study_program = NULL');
    DB::statement('UPDATE students SET period_start = NULL');
    DB::statement('UPDATE students SET period_end = NULL');
    DB::statement('UPDATE students SET directorate = NULL');

    DB::statement('UPDATE supervisors SET employee_id = NULL');
    DB::statement('UPDATE supervisors SET position = NULL');
    DB::statement('UPDATE supervisors SET directorate = NULL');
}
```

- [ ] **Step 3: Run the backfill migration**

Run: `php artisan migrate`

Expected: Data copied from old columns to new columns.

- [ ] **Step 4: Verify backfill via tinker**

Run: `php artisan tinker`

Then execute:
```php
$student = App\Models\Student::first();
echo "Old: " . $student->getAttribute('universitas') . ", New: " . $student->getAttribute('university');
```

Expected: Both old and new columns have the same value.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/
git commit -m "chore: Add migration to backfill normalized columns with existing data"
```

---

### Task 5: Update Model Fillables and Attributes (Student Model)

**Files:**
- Modify: `app/Models/Student.php`

- [ ] **Step 1: Read Student model**

Run: `cat app/Models/Student.php | head -50`

Note the current `$fillable` array and any casts.

- [ ] **Step 2: Update Student model $fillable array**

In `app/Models/Student.php`, find the `$fillable` property and update:

From:
```php
protected $fillable = ['user_id', 'supervisor_id', 'nim', 'universitas', 'program_studi', 'semester', 'direktorat', 'periode_mulai', 'periode_selesai'];
```

To:
```php
protected $fillable = ['user_id', 'supervisor_id', 'nim', 'university', 'study_program', 'semester', 'directorate', 'period_start', 'period_end'];
```

- [ ] **Step 3: Update any $casts if they reference old columns**

If the model has a `$casts` array, update column names there too:

From: `'periode_mulai' => 'date'` → To: `'period_start' => 'date'`

- [ ] **Step 4: Update any custom accessor/mutator methods**

Search for methods like `getUniversitasAttribute()` or `setUniversitasAttribute()` — rename to match new column names if they exist.

- [ ] **Step 5: Verify Student model syntax**

Run: `php -l app/Models/Student.php`

Expected: No syntax errors.

- [ ] **Step 6: Test Student model in tinker**

Run: `php artisan tinker`

Then:
```php
$student = App\Models\Student::first();
echo $student->university;  // Should not error
echo $student->study_program; // Should not error
```

Expected: No errors, values display correctly.

- [ ] **Step 7: Commit**

```bash
git add app/Models/Student.php
git commit -m "refactor: Update Student model to use normalized column names"
```

---

### Task 6: Update Model Fillables and Attributes (Supervisor Model)

**Files:**
- Modify: `app/Models/Supervisor.php`

- [ ] **Step 1: Read Supervisor model**

Run: `cat app/Models/Supervisor.php | head -50`

- [ ] **Step 2: Update Supervisor model $fillable array**

From:
```php
protected $fillable = ['user_id', 'nip', 'jabatan', 'direktorat'];
```

To:
```php
protected $fillable = ['user_id', 'employee_id', 'position', 'directorate'];
```

- [ ] **Step 3: Update any $casts or methods if they reference old columns**

Similar to Student model, update any accessors/mutators.

- [ ] **Step 4: Verify Supervisor model syntax**

Run: `php -l app/Models/Supervisor.php`

Expected: No syntax errors.

- [ ] **Step 5: Test Supervisor model in tinker**

Run: `php artisan tinker`

Then:
```php
$supervisor = App\Models\Supervisor::first();
echo $supervisor->employee_id;  // Should not error
echo $supervisor->position;     // Should not error
```

Expected: No errors, values display correctly.

- [ ] **Step 6: Commit**

```bash
git add app/Models/Supervisor.php
git commit -m "refactor: Update Supervisor model to use normalized column names"
```

---

### Task 7: Update Task Model (Enum Values)

**Files:**
- Modify: `app/Models/Task.php`

- [ ] **Step 1: Read Task model**

Run: `cat app/Models/Task.php | grep -A 5 "type"`

Look for enum definition or cast configuration for the `type` column.

- [ ] **Step 2: Update Task enum values in migration**

If Task uses a native Laravel enum, you'll need a separate migration:

Run: `php artisan make:migration update_task_type_enum --create=false`

Then add:
```php
public function up(): void
{
    DB::statement("ALTER TABLE tasks MODIFY type ENUM('daily', 'final') DEFAULT 'daily'");
}

public function down(): void
{
    DB::statement("ALTER TABLE tasks MODIFY type ENUM('harian', 'akhir') DEFAULT 'harian'");
}
```

- [ ] **Step 3: Run the migration**

Run: `php artisan migrate`

Expected: Enum values updated.

- [ ] **Step 4: If Task model uses a custom cast or accessor, update it**

Search for any references to 'harian' or 'akhir' in the Task model and update to 'daily' and 'final'.

- [ ] **Step 5: Verify Task model**

Run: `php -l app/Models/Task.php`

Expected: No syntax errors.

- [ ] **Step 6: Commit**

```bash
git add app/Models/Task.php database/migrations/
git commit -m "refactor: Update Task model enum values to English (daily/final)"
```

---

### Task 8: Update Controllers (Search and Replace Old Column References)

**Files:**
- Modify: `app/Http/Controllers/Supervisor/*.php`
- Modify: `app/Http/Controllers/Admin/*.php`
- Modify: `app/Http/Controllers/Student/*.php`

- [ ] **Step 1: Find all references to old column names in controllers**

Run: `grep -rn "universitas\|program_studi\|periode_mulai\|periode_selesai\|direktorat\|->nip\|->jabatan" app/Http/Controllers/ --include="*.php" | head -30`

Note all matches and their line numbers.

- [ ] **Step 2: Update each controller file**

For each match found:
- Open the file
- Find the exact line with the old column reference
- Replace with the new column name

Examples:
- `$student->universitas` → `$student->university`
- `$student->program_studi` → `$student->study_program`
- `$supervisor->nip` → `$supervisor->employee_id`
- `$supervisor->jabatan` → `$supervisor->position`

- [ ] **Step 3: Update validation rules in controllers**

Search for validation rule definitions:

Run: `grep -rn "'universitas'\|'program_studi'\|'periode_mulai'" app/Http/Controllers/ --include="*.php"`

Update field names in validation arrays to match new column names.

- [ ] **Step 4: Verify no more old references exist in controllers**

Run: `grep -rn "universitas\|program_studi\|periode_mulai\|periode_selesai" app/Http/Controllers/ --include="*.php"`

Expected: No results (or only in comments explaining the change).

- [ ] **Step 5: Verify syntax of all modified controller files**

Run: `for f in app/Http/Controllers/**/*.php; do php -l "$f" || echo "FAIL: $f"; done`

Expected: No syntax errors reported.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/
git commit -m "refactor: Update controller references to use normalized column names"
```

---

### Task 9: Update React Components (Search and Replace Old Column References)

**Files:**
- Modify: `resources/js/Pages/**/*.jsx`
- Modify: `resources/js/Components/**/*.jsx`

- [ ] **Step 1: Find all references to old column names in React components**

Run: `grep -rn "universitas\|program_studi\|periode_mulai\|periode_selesai\|direktorat" resources/js/ --include="*.jsx" | head -20`

Note all matches.

- [ ] **Step 2: Update each React component**

For each match:
- Open the file
- Replace old column names with new ones

Examples:
- `student.universitas` → `student.university`
- `student.program_studi` → `student.study_program`
- `supervisor.nip` → `supervisor.employee_id`
- Form input names: `name="universitas"` → `name="university"`

- [ ] **Step 3: Update form submissions in React**

Search for form data objects that include old column names:

Run: `grep -rn "universitas:\|program_studi:\|nip:" resources/js/ --include="*.jsx"`

Update all form data objects to use new column names.

- [ ] **Step 4: Verify no more old references exist in React**

Run: `grep -rn "universitas\|program_studi\|periode_" resources/js/ --include="*.jsx" | grep -v "// " | grep -v "/\*"`

Expected: No results (excluding comments).

- [ ] **Step 5: Verify React syntax (if linter available)**

Run: `npm run lint resources/js/`

Expected: No critical errors related to the changes.

- [ ] **Step 6: Commit**

```bash
git add resources/js/
git commit -m "refactor: Update React components to use normalized column names"
```

---

### Task 10: Update Seeders and Factories

**Files:**
- Modify: `database/seeders/StudentSeeder.php`
- Modify: `database/factories/StudentFactory.php`
- Modify: `database/seeders/SupervisorSeeder.php` (if exists)
- Modify: `database/factories/SupervisorFactory.php` (if exists)

- [ ] **Step 1: Read StudentSeeder**

Run: `cat database/seeders/StudentSeeder.php`

- [ ] **Step 2: Update StudentSeeder column names**

Replace old column names with new ones:
- `'universitas'` → `'university'`
- `'program_studi'` → `'study_program'`
- `'periode_mulai'` → `'period_start'`
- `'periode_selesai'` → `'period_end'`
- `'direktorat'` → `'directorate'`

- [ ] **Step 3: Update StudentFactory**

Run: `cat database/factories/StudentFactory.php`

Replace old column names with new ones, same as above.

- [ ] **Step 4: Update SupervisorSeeder (if exists)**

Run: `cat database/seeders/SupervisorSeeder.php 2>/dev/null || echo "File not found"`

If exists, replace:
- `'nip'` → `'employee_id'`
- `'jabatan'` → `'position'`
- `'direktorat'` → `'directorate'`

- [ ] **Step 5: Update SupervisorFactory (if exists)**

Similar to above if the factory exists.

- [ ] **Step 6: Update Task enum values in seeder (if hardcoded)**

Search for any hardcoded task type values:

Run: `grep -rn "'harian'\|'akhir'" database/seeders/ --include="*.php"`

Replace:
- `'harian'` → `'daily'`
- `'akhir'` → `'final'`

- [ ] **Step 7: Verify seeder/factory syntax**

Run: `php -l database/seeders/StudentSeeder.php && php -l database/factories/StudentFactory.php`

Expected: No syntax errors.

- [ ] **Step 8: Test seeders**

Run: `php artisan tinker --execute="require 'database/seeders/StudentSeeder.php'; (new StudentSeeder())->run();"`

Expected: No errors, seeders run successfully.

- [ ] **Step 9: Commit**

```bash
git add database/seeders/ database/factories/
git commit -m "refactor: Update seeders and factories to use normalized column names"
```

---

### Task 11: Update Tests (If Any Exist)

**Files:**
- Modify: `tests/Feature/**/*.php`
- Modify: `tests/Unit/**/*.php`

- [ ] **Step 1: Find any test references to old column names**

Run: `grep -rn "universitas\|program_studi\|periode_mulai" tests/ --include="*.php" 2>/dev/null || echo "No tests found"`

- [ ] **Step 2: Update test assertions**

For each match found in tests:
- Replace old column names with new ones
- Update any assertions that check for old column names

- [ ] **Step 3: Update test data creation**

Update any `create()` or `make()` calls that reference old columns:

From: `Student::create(['universitas' => 'Test'])`
To: `Student::create(['university' => 'Test'])`

- [ ] **Step 4: Verify test syntax**

Run: `php -l tests/Feature/*.php tests/Unit/*.php 2>/dev/null | grep -i error || echo "All test files valid"`

- [ ] **Step 5: Run the test suite**

Run: `php artisan test`

Expected: All tests pass (or pass at same rate as before).

- [ ] **Step 6: Commit**

```bash
git add tests/
git commit -m "test: Update test assertions to use normalized column names"
```

---

### Task 12: Create Cleanup Migration (Drop Old Columns)

**Files:**
- Create: `database/migrations/YYYY_MM_DD_drop_old_normalized_columns.php`

- [ ] **Step 1: Create cleanup migration**

Run: `php artisan make:migration drop_old_normalized_columns --create=false`

- [ ] **Step 2: Write cleanup migration**

```php
public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn(['universitas', 'program_studi', 'periode_mulai', 'periode_selesai', 'direktorat']);
    });

    Schema::table('supervisors', function (Blueprint $table) {
        $table->dropColumn(['nip', 'jabatan', 'direktorat']);
    });
}

public function down(): void
{
    // Restore old columns (reverse of the add migration)
    Schema::table('students', function (Blueprint $table) {
        $table->string('universitas')->nullable();
        $table->string('program_studi')->nullable();
        $table->date('periode_mulai')->nullable();
        $table->date('periode_selesai')->nullable();
        $table->string('direktorat')->nullable();
    });

    Schema::table('supervisors', function (Blueprint $table) {
        $table->string('nip')->nullable();
        $table->string('jabatan')->nullable();
        $table->string('direktorat')->nullable();
    });
}
```

- [ ] **Step 3: Run the cleanup migration**

Run: `php artisan migrate`

Expected: Old columns successfully dropped.

- [ ] **Step 4: Verify old columns are gone**

Run: `php artisan tinker --execute="echo json_encode(\\DB::getSchemaBuilder()->getColumnListing('students'), JSON_PRETTY_PRINT);"`

Expected: Old columns (`universitas`, `program_studi`, `periode_mulai`, `periode_selesai`, `direktorat`) are no longer in the list.

- [ ] **Step 5: Test application still works**

Run through manual verification checklist:
- Admin > Create Student → Should save without errors
- Supervisor Dashboard → Should display correctly
- Student Info → Should show all fields

- [ ] **Step 6: Commit**

```bash
git add database/migrations/
git commit -m "chore: Remove old database columns, complete normalization migration"
```

---

### Task 13: Final Verification and Documentation

**Files:**
- No files to modify; verification only

- [ ] **Step 1: Run full test suite one more time**

Run: `php artisan test`

Expected: All tests pass.

- [ ] **Step 2: Verify no references to old column names remain**

Run: `grep -r "universitas\|program_studi\|periode_mulai\|periode_selesai\|->nip\|->jabatan\|actionlogin\|actionlogout\|resend_activation\|students\.list\.index" app/ resources/ database/ --include="*.php" --include="*.jsx" | grep -v "migration\|comment" || echo "No old references found"`

Expected: No results (excluding migration files and comments explaining the deprecation).

- [ ] **Step 3: Check route list for renamed routes**

Run: `php artisan route:list | grep -E "resend-activation|students\.index"`

Expected: Both routes appear with correct names.

- [ ] **Step 4: Verify database schema is clean**

Run: `php artisan tinker --execute="foreach(['students', 'supervisors'] as \$table) { echo \$table . \": \" . implode(', ', \\DB::getSchemaBuilder()->getColumnListing(\$table)) . \"\\n\"; }"`

Expected: Only new column names appear (no old ones).

- [ ] **Step 5: Document the normalization in CHANGELOG (if exists)**

If a CHANGELOG.md exists, add a note:
```
## Unreleased

### Changed
- Database columns normalized from Indonesian to English
- Controller methods: `actionlogin()` → `login()`, `actionlogout()` → `logout()`
- Route names standardized: `resend_activation` → `resend-activation`, `students.list.index` → `students.index`
- Task type enum values: `harian` → `daily`, `akhir` → `final`
```

- [ ] **Step 6: Final commit summarizing the work**

```bash
git log --oneline | head -15
git commit --allow-empty -m "docs: Naming normalization complete — all Indonesian database columns migrated to English"
```

---

## Testing Approach

No additional test files need to be created — use the existing test suite to verify:

- Feature tests exercise controller methods and form submissions
- Database migrations are reversible (test `php artisan migrate:rollback`)
- Seeders run without errors

---

## Success Criteria

- ✅ All old column references removed from code
- ✅ All controller method names updated
- ✅ All route names updated
- ✅ Database migrations run without error
- ✅ All tests pass
- ✅ Old columns successfully dropped from database
- ✅ Application functions normally after cleanup migration

