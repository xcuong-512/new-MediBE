<?php

namespace App\Services;

use App\Models\DoctorSlot;
use Carbon\Carbon;

class DoctorSlotService
{

    public static function generateSlots(
        int $doctorProfileId,
        int $clinicBranchId,
        string $fromDate,
        int $days = 90,
        string $startTime = "08:00",
        string $endTime = "17:00",
        int $slotMinutes = 30,
        array $workDays = [1,2,3,4,5]
    ): int
    {
        $created = 0;

        $from = Carbon::parse($fromDate)->startOfDay();
        $to = $from->copy()->addDays($days);

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {

            if (!in_array($date->dayOfWeekIso, $workDays)) {
                continue;
            }

            $dayStr = $date->toDateString();

            $start = Carbon::parse($dayStr . " " . $startTime);
            $end = Carbon::parse($dayStr . " " . $endTime);

            while ($start->lt($end)) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes($slotMinutes);

                if ($slotEnd->gt($end)) break;

                $exists = DoctorSlot::query()
                    ->where('doctor_profile_id', $doctorProfileId)
                    ->where('date', $dayStr)
                    ->where('start_time', $slotStart->format('H:i:s'))
                    ->exists();

                if (!$exists) {
                    DoctorSlot::create([
                        'doctor_profile_id' => $doctorProfileId,
                        'clinic_branch_id' => $clinicBranchId,
                        'date' => $dayStr,
                        'start_time' => $slotStart->format('H:i:s'),
                        'end_time' => $slotEnd->format('H:i:s'),
                        'status' => 'available',
                    ]);
                    $created++;
                }

                $start->addMinutes($slotMinutes);
            }
        }

        return $created;
    }
}
