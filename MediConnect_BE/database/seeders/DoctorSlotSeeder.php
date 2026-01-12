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
        $days = 14;
        $startDate = Carbon::today();

        $workingHours = DoctorWorkingHour::all();

        foreach ($workingHours as $wh) {
            for ($d = 0; $d < $days; $d++) {
                $date = $startDate->copy()->addDays($d);

                if ((int)$date->dayOfWeek !== (int)$wh->day_of_week) {
                    continue;
                }

                $start = Carbon::parse($date->toDateString() . ' ' . $wh->start_time);
                $end = Carbon::parse($date->toDateString() . ' ' . $wh->end_time);

                while ($start->copy()->addMinutes($wh->slot_minutes)->lte($end)) {
                    $slotStart = $start->format('H:i:s');
                    $slotEnd = $start->copy()->addMinutes($wh->slot_minutes)->format('H:i:s');

                    DoctorSlot::firstOrCreate([
                        'doctor_profile_id' => $wh->doctor_profile_id,
                        'clinic_branch_id' => $wh->clinic_branch_id,
                        'date' => $date->toDateString(),
                        'start_time' => $slotStart,
                    ], [
                        'end_time' => $slotEnd,
                        'status' => 'available',
                        'generated_from_working_hour_id' => $wh->id,
                    ]);

                    $start->addMinutes($wh->slot_minutes);
                }
            }
        }
    }
}
