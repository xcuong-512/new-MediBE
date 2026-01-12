<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\AppointmentService;

class AdminAppointmentController extends Controller
{
    public function confirm($id, Request $request)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status !== 'pending') {
            return response()->json([
                'message' => 'Chỉ có thể confirm lịch pending'
            ], 409);
        }

        $from = $appointment->status;

        $appointment->update(['status' => 'confirmed']);

        AppointmentService::logStatus(
            $appointment,
            $request->user()->id,
            $from,
            'confirmed',
            'Admin confirm lịch'
        );

        return response()->json([
            'message' => 'Đã confirm lịch',
            'data' => $appointment
        ]);
    }

    public function complete($id, Request $request)
    {
        $appointment = Appointment::findOrFail($id);

        if (!in_array($appointment->status, ['confirmed', 'checkin'])) {
            return response()->json([
                'message' => 'Chỉ complete lịch đã confirmed/checkin'
            ], 409);
        }

        $from = $appointment->status;

        $appointment->update(['status' => 'completed']);

        AppointmentService::logStatus(
            $appointment,
            $request->user()->id,
            $from,
            'completed',
            'Admin complete lịch'
        );

        return response()->json([
            'message' => 'Đã complete lịch',
            'data' => $appointment
        ]);
    }
}
