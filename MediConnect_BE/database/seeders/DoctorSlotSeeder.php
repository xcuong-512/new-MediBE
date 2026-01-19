<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\DoctorWorkingHour;
use App\Models\DoctorSlot;

class DoctorSlotSeeder extends Seeder
{
    public function run(): void
    {
        $days = 90;
        $startDate = Carbon::today();

        $workingHours = DoctorWorkingHour::query()->get();

        if ($workingHours->isEmpty()) {
            return;
        }

        foreach ($workingHours as $wh) {
            for ($d = 0; $d < $days; $d++) {
                $date = $startDate->copy()->addDays($d);

                if ((int) $date->dayOfWeek !== (int) $wh->day_of_week) {
                    continue;
                }

                $start = Carbon::parse($date->toDateString() . ' ' . $wh->start_time);
                $end = Carbon::parse($date->toDateString() . ' ' . $wh->end_time);

                $slotMinutes = (int) ($wh->slot_minutes ?? 30);
                if ($slotMinutes <= 0) $slotMinutes = 30;

                while ($start->copy()->addMinutes($slotMinutes)->lte($end)) {
                    $slotStart = $start->format('H:i:s');
                    $slotEnd = $start->copy()->addMinutes($slotMinutes)->format('H:i:s');

                    DoctorSlot::updateOrCreate(
                        [
                            'doctor_profile_id' => $wh->doctor_profile_id,
                            'date' => $date->toDateString(),
                            'start_time' => $slotStart,
                        ],
                        [
                            'clinic_branch_id' => $wh->clinic_branch_id,
                            'end_time' => $slotEnd,
                            'status' => 'available',
                            'generated_from_working_hour_id' => $wh->id,
                        ]
                    );

                    $start->addMinutes($slotMinutes);
                }
            }
        }
    }
}
