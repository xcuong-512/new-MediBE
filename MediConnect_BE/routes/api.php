<?php

use App\Http\Controllers\Api\AdminAppointmentController;
use App\Http\Controllers\Api\AdminSlotController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorAppointmentController;
use App\Http\Controllers\Api\ReviewController;

Route::get('/specialties', [SpecialtyController::class, 'index']);

Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);


Route::get('/doctors/{id}/available-slots', [SlotController::class, 'availableSlots']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/doctors/{id}/reviews', [ReviewController::class, 'doctorReviews']);
Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::middleware('role:patient')->group(function () {
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::get('/appointments/my', [AppointmentController::class, 'myAppointments']);
        Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
    });

    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/appointments', [DoctorAppointmentController::class, 'index']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::patch('/admin/appointments/{id}/confirm', [AdminAppointmentController::class, 'confirm']);
        Route::patch('/admin/appointments/{id}/complete', [AdminAppointmentController::class, 'complete']);
        Route::post('/admin/slots/generate', [AdminSlotController::class, 'generate']);
    });
});
