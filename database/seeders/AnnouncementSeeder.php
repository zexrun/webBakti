<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 5-6 announcements from the admin user, mostly published (mixed
 * priority) with one or two left as drafts (published_at null) so the
 * Admin Announcements page shows both states.
 */
class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('Skipped: no admin user found.');
            return;
        }

        Announcement::factory()->count(4)->create(['admin_id' => $admin->id]);
        Announcement::factory()->draft()->count(2)->create(['admin_id' => $admin->id]);
    }
}
