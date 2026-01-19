<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DoctorProfileController extends Controller
{

    public function show(Request $request)
    {
        $user = $request->user();

        $profile = DoctorProfile::with([
            'specialty:id,name',
        ])
            ->where('user_id', $user->id)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor profile not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,

                    'role_id' => $user->role_id,
                ],
                'profile' => [
                    'id' => $profile->id,
                    'specialty_id' => $profile->specialty_id,
                    'specialty' => $profile->specialty,

                    'license_number' => $profile->license_number,
                    'bio' => $profile->bio,
                    'experience_years' => $profile->experience_years,
                    'consultation_fee' => $profile->consultation_fee,

                    'is_active' => (bool) $profile->is_active,
                ]
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $profile = DoctorProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor profile not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($user->id)
            ],

            'specialty_id' => ['nullable', Rule::exists('specialties', 'id')],
            'license_number' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            if (!empty($user->avatar_url) && str_contains($user->avatar_url, '/storage/')) {
                $old = str_replace(asset('') . 'storage/', '', $user->avatar_url); 
                $old = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($old);
            }

            $user->avatar_url = asset('storage/' . $path);
        }

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        $profile->update([
            'specialty_id' => $validated['specialty_id'] ?? $profile->specialty_id,
            'license_number' => $validated['license_number'] ?? $profile->license_number,
            'bio' => $validated['bio'] ?? $profile->bio,
            'experience_years' => $validated['experience_years'] ?? $profile->experience_years,
            'consultation_fee' => $validated['consultation_fee'] ?? $profile->consultation_fee,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'avatar_url' => $user->avatar_url,
            ]
        ]);
    }
}
