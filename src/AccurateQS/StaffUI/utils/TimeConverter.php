<?php

namespace AccurateQS\StaffUI\utils;

class TimeConverter {

    public static function secondsToHuman(int $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) $parts[] = "$days jour" . ($days > 1 ? "s" : "");
        if ($hours > 0) $parts[] = "$hours heure" . ($hours > 1 ? "s" : "");
        if ($minutes > 0) $parts[] = "$minutes minute" . ($minutes > 1 ? "s" : "");

        return implode(", ", $parts);
    }

    public static function humanToSeconds(int $amount, string $unit): int {
        switch ($unit) {
            case 'm':
                return $amount * 60;
            case 'h':
                return $amount * 3600;
            case 'd':
                return $amount * 86400;
            default:
                return $amount;
        }
    }
}
