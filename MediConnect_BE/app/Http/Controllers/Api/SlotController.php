<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSlot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function availableSlots($doctorId, Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = $request->query('date');

        $slots = DoctorSlot::query()
            ->where('doctor_profile_id', $doctorId)
            ->where('date', $date)
            ->where('status', 'available')
            ->orderBy('start_time')
            ->get([
                'id',
                'date',
                'start_time',
                'end_time',
                'status',
            ]);

        return response()->json([
            'doctor_id' => (int)$doctorId,
            'date' => $date,
            'data' => $slots
        ]);
    }
}
