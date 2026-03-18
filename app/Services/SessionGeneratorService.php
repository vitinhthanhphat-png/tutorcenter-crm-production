<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\ClassSession;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Generates ClassSession rows from a ClassRoom's schedule_rule JSON.
 *
 * Schedule rule format:
 * {
 *   "days": ["monday", "wednesday", "friday"],
 *   "start_time": "18:00",
 *   "end_time": "20:00"
 * }
 */
class SessionGeneratorService
{
    /**
     * Generate sessions for a class from a start date to an end date.
     *
     * @param  ClassRoom $class
     * @param  string    $fromDate   Y-m-d
     * @param  string    $toDate     Y-m-d
     * @return int  Number of sessions created
     */
    public function generateForClass(ClassRoom $class, string $fromDate, string $toDate): int
    {
        $rule = $class->schedule_rule;

        if (empty($rule['days']) || empty($rule['start_time']) || empty($rule['end_time'])) {
            return 0;
        }

        $created = 0;
        $period  = CarbonPeriod::create($fromDate, $toDate);

        // Map day names (english lowercase) → Carbon weekday number
        $dayMap = [
            'monday'    => Carbon::MONDAY,
            'tuesday'   => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday'  => Carbon::THURSDAY,
            'friday'    => Carbon::FRIDAY,
            'saturday'  => Carbon::SATURDAY,
            'sunday'    => Carbon::SUNDAY,
        ];

        $targetDays = array_filter(
            array_map(fn($d) => $dayMap[strtolower($d)] ?? null, $rule['days'])
        );

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $targetDays)) {
                continue;
            }

            // Skip if session already exists for this date
            $exists = ClassSession::where('class_id', $class->id)
                ->where('date', $date->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            ClassSession::create([
                'tenant_id'  => $class->tenant_id,
                'class_id'   => $class->id,
                'teacher_id' => $class->teacher_id,
                'date'       => $date->toDateString(),
                'start_time' => $rule['start_time'],
                'end_time'   => $rule['end_time'],
                'type'       => 'regular',
                'status'     => 'scheduled',
            ]);

            $created++;
        }

        return $created;
    }

    /**
     * Generate sessions for the current month for a class.
     */
    public function generateCurrentMonth(ClassRoom $class): int
    {
        return $this->generateForClass(
            $class,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );
    }

    /**
     * Generate sessions for ALL active classes for the given month.
     * Called by artisan command / scheduler.
     */
    public function generateForAllActive(string $fromDate = null, string $toDate = null): array
    {
        $from = $fromDate ?? now()->startOfMonth()->toDateString();
        $to   = $toDate   ?? now()->endOfMonth()->toDateString();

        $classes = ClassRoom::where('status', 'active')->get();
        $report  = [];

        foreach ($classes as $class) {
            $count = $this->generateForClass($class, $from, $to);
            $report[$class->name] = $count;
        }

        return $report;
    }
}
