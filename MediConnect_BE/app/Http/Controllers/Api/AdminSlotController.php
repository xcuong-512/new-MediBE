<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\DoctorWorkingHour;
use App\Models\DoctorSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSlotController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'doctor_id' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
        ]);

        $days = $data['days'] ?? 30;

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])->startOfDay()
            : Carbon::today();

        $doctorId = $data['doctor_id'] ?? null;

        $workingHoursQuery = DoctorWorkingHour::query();

        if ($doctorId) {
            $workingHoursQuery->where('doctor_profile_id', $doctorId);
        }

        $workingHours = $workingHoursQuery->get();

        if ($workingHours->isEmpty()) {
            return ApiResponse::error('Không có working hours để generate slot', null, 404);
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($workingHours, $startDate, $days, &$created, &$updated) {

            foreach ($workingHours as $wh) {
                for ($i = 0; $i < $days; $i++) {

                    $date = $startDate->copy()->addDays($i);

                    // chỉ tạo slot nếu date đúng thứ
                    if ((int)$date->dayOfWeek !== (int)$wh->day_of_week) {
                        continue;
                    }

                    $start = Carbon::parse($date->toDateString() . ' ' . $wh->start_time);
                    $end = Carbon::parse($date->toDateString() . ' ' . $wh->end_time);

                    $slotMinutes = (int)($wh->slot_minutes ?? 30);
                    if ($slotMinutes <= 0) $slotMinutes = 30;

                    while ($start->copy()->addMinutes($slotMinutes)->lte($end)) {

                        $slotStart = $start->format('H:i:s');
                        $slotEnd = $start->copy()->addMinutes($slotMinutes)->format('H:i:s');

                        /**
                         * ✅ IMPORTANT:
                         * Unique index của doctor_slots là:
                         * (doctor_profile_id, date, start_time)
                         *
                         * => condition của updateOrCreate phải dùng đúng các field unique,
                         * không được include clinic_branch_id nữa.
                         */
                        $slot = DoctorSlot::updateOrCreate(
                            [
                                'doctor_profile_id' => $wh->doctor_profile_id,
                                'date' => $date->toDateString(),
                                'start_time' => $slotStart,
                            ],
                            [
                                'clinic_branch_id' => $wh->clinic_branch_id,
                                'end_time' => $slotEnd,
                                'generated_from_working_hour_id' => $wh->id,

                                // chỉ set available nếu slot đang không booked
                                // (nếu booked/cancelled thì giữ nguyên)
                                'status' => DB::raw("IF(status='booked', status, 'available')")
                            ]
                        );

                        if ($slot->wasRecentlyCreated) {
                            $created++;
                        } else {
                            $updated++;
                        }

                        $start->addMinutes($slotMinutes);
                    }
                }
            }
        });

        return ApiResponse::success([
            'days' => $days,
            'start_date' => $startDate->toDateString(),
            'created_slots' => $created,
            'updated_slots' => $updated
        ], 'Generate slots thành công');
    }
}
