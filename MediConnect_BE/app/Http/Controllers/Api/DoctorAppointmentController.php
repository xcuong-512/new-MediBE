<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $user = $request->user();

        $doctorProfile = DoctorProfile::where('user_id', $user->id)->first();

        if (!$doctorProfile) {
            return response()->json([
                'message' => 'Bạn không phải bác sĩ'
            ], 403);
        }

        $query = Appointment::query()
            ->where('doctor_profile_id', $doctorProfile->id)
            ->with([
                'patient:id,name,email,phone'
            ])
            ->orderBy('date')
            ->orderBy('start_time');

        if ($request->filled('date')) {
            $query->where('date', $request->query('date'));
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }
}
