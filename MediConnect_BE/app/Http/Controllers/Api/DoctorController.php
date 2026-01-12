<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class DoctorController extends Controller
{
    // GET /api/doctors?specialty_id=&q=
    public function index(Request $request)
    {
        $q = $request->query('q');
        $specialtyId = $request->query('specialty_id');

        $doctors = DoctorProfile::query()
            ->where('is_active', true)
            ->with([
                'user:id,name,email,phone,avatar_url',
                'specialty:id,name,slug'
            ])
            ->when($specialtyId, function ($query) use ($specialtyId) {
                $query->where('specialty_id', $specialtyId);
            })
            ->when($q, function ($query) use ($q) {
                $query->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('rating_avg')
            ->paginate(10);

        return ApiResponse::success($doctors, 'OK');
    }

    // GET /api/doctors/{id}
    public function show($id)
    {
        $doctor = DoctorProfile::query()
            ->where('is_active', true)
            ->with([
                'user:id,name,email,phone,avatar_url',
                'specialty:id,name,slug'
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => $doctor
        ]);
    }
}
