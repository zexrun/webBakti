<?php

namespace Database\Seeders;

use Database\Seeders\AdminSeeder;
use Database\Seeders\DirectorateSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\SupervisorSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\BulkUserSeeder;
use Database\Seeders\StudentSupervisorAssignmentSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\UniversitySeeder;
use Database\Seeders\AttendanceSettingSeeder;
use Database\Seeders\AttendanceSeeder;
use Database\Seeders\AttendanceExceptionSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\FinalAssessmentSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\LogbookSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\AnnouncementSeeder;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            DirectorateSeeder::class,
            PositionSeeder::class,
            SupervisorSeeder::class,
            StudentSeeder::class,
            BulkUserSeeder::class,
            StudentSupervisorAssignmentSeeder::class,
            UniversitySeeder::class,
            AttendanceSettingSeeder::class,
            AttendanceSeeder::class,
            AttendanceExceptionSeeder::class,
            TaskSeeder::class,
            SubmissionSeeder::class,
            FinalAssessmentSeeder::class,
            DocumentSeeder::class,
            LogbookSeeder::class,
            MessageSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
