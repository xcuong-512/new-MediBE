<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $appointment = Appointment::query()
            ->where('id', $data['appointment_id'])
            ->where('patient_id', $user->id)
            ->firstOrFail();

        if ($appointment->status !== 'completed') {
            return ApiResponse::error('Chỉ được đánh giá sau khi khám xong (completed)', null, 409);
        }


        if (Review::where('appointment_id', $appointment->id)->exists()) {
            return ApiResponse::error('Lịch này đã được đánh giá rồi', null, 409);
        }

        $review = DB::transaction(function () use ($data, $appointment, $user) {

            $review = Review::create([
                'patient_id' => $user->id,
                'doctor_profile_id' => $appointment->doctor_profile_id,
                'appointment_id' => $appointment->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $doctor = DoctorProfile::findOrFail($appointment->doctor_profile_id);

            $stats = Review::where('doctor_profile_id', $doctor->id)
                ->selectRaw('COUNT(*) as total, AVG(rating) as avg_rating')
                ->first();

            $doctor->update([
                'total_reviews' => (int)$stats->total,
                'rating_avg' => round((float)$stats->avg_rating, 2),
            ]);

            return $review;
        });

        return ApiResponse::success($review, 'Đánh giá thành công', 201);
    }


    public function doctorReviews($doctorId)
    {
        $reviews = Review::query()
            ->where('doctor_profile_id', $doctorId)
            ->with(['patient:id,name,avatar_url'])
            ->orderByDesc('id')
            ->paginate(10);

        return ApiResponse::success($reviews, 'OK');
    }
}
