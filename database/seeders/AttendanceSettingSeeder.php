<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use Illuminate\Database\Seeder;

/**
 * AttendanceSetting::getSettings() already lazily creates a default row
 * if none exists - this seeder just guarantees that happens during a
 * fresh seed run, so the row exists deterministically rather than on
 * whichever request happens to touch it first.
 */
class AttendanceSettingSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceSetting::getSettings();
    }
}
