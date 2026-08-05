<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Adds 100 additional users on top of the hand-picked accounts from
 * AdminSeeder/SupervisorSeeder/StudentSeeder (10 admin, 40 supervisor,
 * 50 student), so features like RecipientPicker's search/grouping and
 * any other listing page have a realistic volume of data to work with.
 *
 * Each of the 50 new students is assigned to one of the 40 new
 * supervisors at random - not round-robin - so some supervisors end up
 * with several students and others with none, matching how a real
 * cohort would distribute rather than an even split.
 *
 * Only touches the users created here; the existing
 * StudentSupervisorAssignmentSeeder's deliberately-unassigned student
 * (used to demo the "belum ada pembimbing" state) is untouched.
 */
class BulkUserSeeder extends Seeder
{
    private const ADMIN_COUNT = 10;
    private const SUPERVISOR_COUNT = 40;
    private const STUDENT_COUNT = 50;

    public function run(): void
    {
        User::factory()->count(self::ADMIN_COUNT)->create(['role' => 'admin']);

        $supervisors = User::factory()
            ->count(self::SUPERVISOR_COUNT)
            ->create(['role' => 'supervisor'])
            ->map(fn (User $user) => $user->supervisor);

        $supervisorIds = $supervisors->pluck('id')->all();

        User::factory()
            ->count(self::STUDENT_COUNT)
            ->create(['role' => 'student'])
            ->each(function (User $user) use ($supervisorIds) {
                $user->student->update([
                    'supervisor_id' => fake()->randomElement($supervisorIds),
                ]);
            });
    }
}
