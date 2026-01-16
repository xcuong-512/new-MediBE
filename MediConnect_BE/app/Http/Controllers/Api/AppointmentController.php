<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AppointmentService;
use App\Helpers\ApiResponse;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => ['required', 'integer'],
            'slot_id' => ['required', 'integer'],
            'type' => ['nullable', 'in:online,offline'],
            'symptom_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($data, $user) {

            // lock slot để tránh 2 người đặt cùng lúc
            $slot = DoctorSlot::query()
                ->where('id', $data['slot_id'])
                ->where('doctor_profile_id', $data['doctor_id'])
                ->lockForUpdate()
                ->first();

            if (!$slot) {
                return ApiResponse::error('Slot không tồn tại', null, 404);
            }

            if ($slot->status !== 'available') {
                return ApiResponse::error('Slot đã được đặt hoặc không khả dụng', null, 409);
            }

            // tạo appointment
            $appointment = Appointment::create([
                'appointment_code' => 'MC-' . strtoupper(Str::random(8)),
                'patient_id' => $user->id,
                'doctor_profile_id' => $slot->doctor_profile_id,
                'clinic_branch_id' => $slot->clinic_branch_id,
                'date' => $slot->date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'type' => $data['type'] ?? 'offline',
                'status' => 'pending',
                'symptom_note' => $data['symptom_note'] ?? null,
            ]);
            AppointmentService::logStatus(
                $appointment,
                $user->id,
                null,
                'pending',
                'Tạo lịch hẹn'
            );

            $slot->update([
                'status' => 'booked'
            ]);

            return ApiResponse::success($appointment, 'Đặt lịch thành công', 201);
        });
    }

    public function myAppointments(Request $request)
{
    $appointments = Appointment::query()
        ->where('patient_id', $request->user()->id)
        ->with([
            'doctorProfile.user:id,name,avatar_url',
            'doctorProfile.specialty:id,name',
            'clinicBranch:id,name,address',
        ])
        ->orderByDesc('date')
        ->orderByDesc('start_time')
        ->paginate(10);

    // normalize payload: add doctor + clinic_branch keys (FE dùng)
    $appointments->getCollection()->transform(function ($appt) {
        return [
            'id' => $appt->id,
            'appointment_code' => $appt->appointment_code,
            'status' => $appt->status,
            'type' => $appt->type,
            'date' => $appt->date,
            'start_time' => $appt->start_time,
            'end_time' => $appt->end_time,

            'doctor' => [
                'consultation_fee' => $appt->doctorProfile?->consultation_fee,
                'experience_years' => $appt->doctorProfile?->experience_years,
                'user' => $appt->doctorProfile?->user,
                'specialty' => $appt->doctorProfile?->specialty,
            ],

            'clinic_branch' => $appt->clinicBranch,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'OK',
        'data' => $appointments
    ]);
}



    public function cancel($id, Request $request)
    {
        $appointment = Appointment::query()
            ->where('id', $id)
            ->where('patient_id', $request->user()->id)
            ->firstOrFail();

        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Không thể hủy lịch này'
            ], 409);
        }
        AppointmentService::logStatus(
            $appointment,
            $request->user()->id,
            $appointment->getOriginal('status'),
            'cancelled',
            'Bệnh nhân hủy lịch'
        );

        DB::transaction(function () use ($appointment) {
            DoctorSlot::query()
                ->where('doctor_profile_id', $appointment->doctor_profile_id)
                ->where('date', $appointment->date)
                ->where('start_time', $appointment->start_time)
                ->update(['status' => 'available']);

            $appointment->update([
                'status' => 'cancelled'
            ]);
        });

        return ApiResponse::success(null, 'Đã hủy lịch');
    }

    public function payDeposit($id, Request $request)
    {
        $appointment = Appointment::query()
            ->where('id', $id)
            ->where('patient_id', $request->user()->id)
            ->firstOrFail();

        if ($appointment->status === 'cancelled') {
            return ApiResponse::error('Lịch đã bị hủy', null, 409);
        }

        if ($appointment->status === 'deposit_paid') {
            return ApiResponse::success($appointment, 'Đã thanh toán đặt cọc trước đó');
        }

        DB::transaction(function () use ($appointment, $request) {
            $appointment->update([
                'status' => 'deposit_paid',
            ]);

            AppointmentService::logStatus(
                $appointment,
                $request->user()->id,
                null,
                'deposit_paid',
                'Thanh toán đặt cọc (demo)'
            );
        });

        return ApiResponse::success($appointment->fresh(), 'Thanh toán đặt cọc thành công');
    }

}
