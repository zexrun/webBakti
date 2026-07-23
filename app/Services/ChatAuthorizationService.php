<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Centralizes "who can message whom" so the recipient picker
 * (allowedRecipients) and the send/reply validation (canMessage) can
 * never drift out of sync - both derive from the same per-role rule:
 *
 *   - admin: everyone else.
 *   - supervisor: the admin(s), plus every student assigned to them.
 *   - student (has a supervisor): the admin(s), their own supervisor,
 *     plus every other student sharing that same supervisor.
 *   - student (no supervisor yet): the admin(s) only.
 */
class ChatAuthorizationService
{
    public function canMessage(User $sender, User $recipient): bool
    {
        return $this->allowedRecipients($sender)->contains('id', $recipient->id);
    }

    public function allowedRecipients(User $sender): Collection
    {
        if ($sender->role === 'admin') {
            return User::where('id', '!=', $sender->id)->get();
        }

        if ($sender->role === 'supervisor') {
            $supervisor = $sender->supervisor;

            if (!$supervisor) {
                return $this->admins($sender);
            }

            $studentUserIds = Student::where('supervisor_id', $supervisor->id)
                ->pluck('user_id');

            return $this->admins($sender)
                ->merge(User::whereIn('id', $studentUserIds)->get());
        }

        if ($sender->role === 'student') {
            $student = $sender->student;

            if (!$student || !$student->supervisor_id) {
                return $this->admins($sender);
            }

            $supervisorUserId = $student->supervisor->user_id ?? null;

            $peerUserIds = Student::where('supervisor_id', $student->supervisor_id)
                ->where('id', '!=', $student->id)
                ->pluck('user_id');

            $recipients = $this->admins($sender)
                ->merge(User::whereIn('id', $peerUserIds)->get());

            if ($supervisorUserId) {
                $recipients = $recipients->merge(User::where('id', $supervisorUserId)->get());
            }

            return $recipients->unique('id')->values();
        }

        return collect();
    }

    private function admins(User $excluding): Collection
    {
        return User::where('role', 'admin')
            ->where('id', '!=', $excluding->id)
            ->get();
    }
}
