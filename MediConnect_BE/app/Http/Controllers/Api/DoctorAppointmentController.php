<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AppointmentService;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $user = $request->user();

        $doctorProfile = DoctorProfile::query()
            ->where('user_id', $user->id)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải bác sĩ',
                'data' => null,
            ], 403);
        }

        $query = Appointment::query()
            ->where('doctor_profile_id', $doctorProfile->id)
            ->with([
                'patient:id,name,email,phone',
                'clinicBranch:id,name,address',
                'doctorProfile.specialty:id,name',
            ])
            ->orderBy('date')
            ->orderBy('start_time');

        if ($request->filled('date')) {
            $query->where('date', $request->query('date'));
        }

        $appointments = $query->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'appointment_code' => $a->appointment_code,
                'status' => $a->status,
                'type' => $a->type,
                'date' => $a->date,
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
                'symptom_note' => $a->symptom_note,

                'patient' => $a->patient ? [
                    'id' => $a->patient->id,
                    'name' => $a->patient->name,
                    'email' => $a->patient->email,
                    'phone' => $a->patient->phone,
                ] : null,

                'clinic_branch' => $a->clinicBranch ? [
                    'id' => $a->clinicBranch->id,
                    'name' => $a->clinicBranch->name,
                    'address' => $a->clinicBranch->address,
                ] : null,

                'specialty' => $a->doctorProfile?->specialty ? [
                    'id' => $a->doctorProfile->specialty->id,
                    'name' => $a->doctorProfile->specialty->name,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $appointments,
        ]);
    }

    public function complete(Request $request, $id)
    {
        $user = $request->user();

        $doctorProfile = DoctorProfile::query()
            ->where('user_id', $user->id)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải bác sĩ',
                'data' => null,
            ], 403);
        }

        $appointment = Appointment::query()
            ->where('id', $id)
            ->where('doctor_profile_id', $doctorProfile->id)
            ->firstOrFail();

        if (in_array($appointment->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hoàn thành lịch này',
            ], 409);
        }

        return DB::transaction(function () use ($appointment, $user) {
            $oldStatus = $appointment->getOriginal('status');

            $appointment->update([
                'status' => 'completed'
            ]);

            AppointmentService::logStatus(
                $appointment,
                $user->id,
                $oldStatus,
                'completed',
                'Bác sĩ xác nhận đã khám xong'
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu hoàn thành',
                'data' => $appointment->fresh()
            ]);
        });
    }
}
