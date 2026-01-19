<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\DoctorSlotService;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DoctorProfile;

class AdminDoctorController extends Controller
{

    public function index()
    {
        $profiles = DoctorProfile::with([
            'user:id,role_id,name,email,phone',
            'specialty:id,name'
        ])
            ->orderByDesc('id')
            ->get();

        $data = $profiles->map(function ($p) {
            $isActive = (bool) $p->is_active;

            return [
                'id' => $p->id,
                'user_id' => $p->user_id,

                'name' => $p->user?->name,
                'email' => $p->user?->email,
                'phone' => $p->user?->phone,
                'role_id' => $p->user?->role_id,

                'specialty_id' => $p->specialty_id,
                'specialty' => $p->specialty ? [
                    'id' => $p->specialty->id,
                    'name' => $p->specialty->name,
                ] : null,

                'is_active' => $isActive,
                'status' => $isActive ? 'active' : 'inactive',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'specialty_id' => ['nullable', Rule::exists('specialties', 'id')],
            'is_active' => ['nullable', 'boolean'],

            'clinic_branch_id' => ['nullable', 'integer', Rule::exists('clinic_branches', 'id')],
        ]);

        return DB::transaction(function () use ($validated) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => 2,
            ]);

            $user->role_id = 2;
            $user->save();

            $profile = DoctorProfile::create([
                'user_id' => $user->id,
                'specialty_id' => $validated['specialty_id'] ?? null,
                'is_active' => array_key_exists('is_active', $validated)
                    ? (bool) $validated['is_active']
                    : true,
            ]);


            $clinicBranchId = (int)($validated['clinic_branch_id'] ?? 1);

            $createdSlots = DoctorSlotService::generateSlots(
                doctorProfileId: $profile->id,
                clinicBranchId: $clinicBranchId,
                fromDate: now()->toDateString(),
                days: 90,
                startTime: "08:00",
                endTime: "17:00",
                slotMinutes: 30,
                workDays: [1,2,3,4,5] 
            );

            return response()->json([
                'success' => true,
                'message' => 'Doctor created successfully',
                'data' => [
                    'doctor_profile_id' => $profile->id,
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'created_slots' => $createdSlots,
                ],
            ], 201);
        });
    }


    public function update(Request $request, $id)
    {
        $profile = DoctorProfile::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($profile->user_id)
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($profile->user_id)
            ],

            'specialty_id' => ['nullable', Rule::exists('specialties', 'id')],
            'is_active' => ['nullable', 'boolean'],
        ]);


        if ($profile->user) {
            $profile->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($profile->user->role_id !== 2) {
                $profile->user->role_id = 2;
                $profile->user->save();
            }
        }

        $profile->update([
            'specialty_id' => $validated['specialty_id'] ?? null,
            'is_active' => array_key_exists('is_active', $validated)
                ? (bool) $validated['is_active']
                : (bool) $profile->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully',
            'data' => [
                'doctor_profile_id' => $profile->id
            ]
        ]);
    }

    public function destroy($id)
    {
        $profile = DoctorProfile::findOrFail($id);
        $userId = $profile->user_id;

        $profile->delete();

        if ($userId) {
            User::where('id', $userId)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully',
        ]);
    }
}
