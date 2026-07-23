<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Assigns specific students to specific supervisors, on top of what
 * SupervisorSeeder/StudentSeeder already create. UserFactory's
 * afterCreating() hook auto-creates the Student/Supervisor rows but
 * never sets supervisor_id (StudentFactory leaves it null), so this
 * seeder exists to make the assignment explicit and reproducible
 * rather than relying on incidental factory/seeder ordering.
 */
class StudentSupervisorAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assign('mrifqy821@gmail.com', 'dede@baktitest.com');
        $this->backfillUnassigned();
    }

    private function assign(string $studentEmail, string $supervisorEmail): void
    {
        $studentUser = User::where('email', $studentEmail)->first();
        $supervisorUser = User::where('email', $supervisorEmail)->first();

        if (!$studentUser || !$studentUser->student) {
            $this->command->warn("Skipped: no student user/record found for {$studentEmail}");
            return;
        }

        if (!$supervisorUser || !$supervisorUser->supervisor) {
            $this->command->warn("Skipped: no supervisor user/record found for {$supervisorEmail}");
            return;
        }

        $studentUser->student->update([
            'supervisor_id' => $supervisorUser->supervisor->id,
        ]);
    }

    /**
     * Round-robin any remaining unassigned students across existing
     * supervisors, so every downstream seeder (Task/Submission/
     * FinalAssessment/Message) can rely on every student having a
     * supervisor - without hardcoding which supervisor gets which
     * leftover student.
     */
    private function backfillUnassigned(): void
    {
        $supervisorIds = Supervisor::pluck('id');

        if ($supervisorIds->isEmpty()) {
            $this->command->warn('Skipped backfill: no supervisors exist yet.');
            return;
        }

        $unassigned = Student::whereNull('supervisor_id')->get();

        foreach ($unassigned as $index => $student) {
            $supervisorId = $supervisorIds[$index % $supervisorIds->count()];
            $student->update(['supervisor_id' => $supervisorId]);
        }
    }
}
