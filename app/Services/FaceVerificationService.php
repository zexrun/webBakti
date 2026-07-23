<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Compares a check-in face descriptor (computed client-side by
 * face-api.js) against a student's stored reference descriptor.
 * Mirrors LocationVerificationService's role: a plain-PHP anomaly
 * check that never blocks the action it's verifying, only flags it
 * for manual review when the signal is bad.
 */
class FaceVerificationService
{
    /**
     * Euclidean distance below this between two descriptors is
     * considered a match, tuned for SsdMobilenetv1 (stricter than the
     * 0.6 commonly quoted for TinyFaceDetector - lowered after logged
     * evidence showed TinyFaceDetector's descriptors didn't separate
     * different people's faces clearly enough at 0.6). Fixed for now;
     * not user-configurable.
     */
    private const MATCH_THRESHOLD = 0.5;

    /**
     * Verify a check-in descriptor against a student's reference.
     *
     * Returns ['status' => string, 'distance' => float|null].
     * status is one of: 'verified', 'mismatch', 'no_reference', 'unavailable'.
     */
    public function verify(?array $checkInDescriptor, ?array $referenceDescriptor, int $userId): array
    {
        if (!$referenceDescriptor) {
            return ['status' => 'no_reference', 'distance' => null];
        }

        if (!$this->isValidDescriptor($checkInDescriptor)) {
            return ['status' => 'unavailable', 'distance' => null];
        }

        if (!$this->isValidDescriptor($referenceDescriptor)) {
            // Malformed reference data (shouldn't normally happen since it's
            // validated at save time in Task 8, but degrade gracefully rather
            // than error if it's ever corrupted).
            return ['status' => 'unavailable', 'distance' => null];
        }

        $distance = $this->euclideanDistance($checkInDescriptor, $referenceDescriptor);
        $status = $distance <= self::MATCH_THRESHOLD ? 'verified' : 'mismatch';

        if ($status === 'mismatch') {
            Log::warning('Face verification mismatch at check-in', [
                'user_id' => $userId,
                'distance' => $distance,
                'threshold' => self::MATCH_THRESHOLD,
            ]);
        }

        return ['status' => $status, 'distance' => round($distance, 4)];
    }

    private function isValidDescriptor($descriptor): bool
    {
        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return false;
        }

        foreach ($descriptor as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }

        return true;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sumSquares = 0.0;

        for ($i = 0; $i < 128; $i++) {
            $diff = (float) $a[$i] - (float) $b[$i];
            $sumSquares += $diff * $diff;
        }

        return sqrt($sumSquares);
    }
}
