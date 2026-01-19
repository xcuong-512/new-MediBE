<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorAppointmentController;
use App\Http\Controllers\Api\AdminAppointmentController;
use App\Http\Controllers\Api\AdminSlotController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminDoctorController;
use App\Http\Controllers\Api\DoctorProfileController;

Route::get('/specialties', [SpecialtyController::class, 'index']);
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);
Route::get('/doctors/{doctorId}/next-available', [DoctorController::class, 'nextAvailableDate']);
Route::get('/doctors/{id}/slots', [SlotController::class, 'availableSlots']);
Route::get('/doctors/{id}/available-slots', [SlotController::class, 'availableSlots']);
Route::get('/doctors/{id}/reviews', [ReviewController::class, 'doctorReviews']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // PATIENT
    Route::middleware('role:patient')->group(function () {
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::get('/appointments/my', [AppointmentController::class, 'myAppointments']);
        Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
        Route::patch('/appointments/{id}/deposit', [AppointmentController::class, 'payDeposit']);
    });

    // DOCTOR
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/appointments', [DoctorAppointmentController::class, 'index']);
        Route::get('/doctor/profile', [DoctorProfileController::class, 'show']);
        Route::post('/doctor/profile', [DoctorProfileController::class, 'update']);
        Route::post('/doctor/appointments/{id}/complete', [DoctorAppointmentController::class, 'complete']);
    });

    // ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/slots/generate', [AdminSlotController::class, 'generate']);

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);

        Route::patch('/admin/appointments/{id}/confirm', [AdminAppointmentController::class, 'confirm']);
        Route::patch('/admin/appointments/{id}/complete', [AdminAppointmentController::class, 'complete']);

        Route::get('/admin/doctors', [AdminDoctorController::class, 'index']);
        Route::post('/admin/doctors', [AdminDoctorController::class, 'store']);
        Route::put('/admin/doctors/{id}', [AdminDoctorController::class, 'update']);
        Route::delete('/admin/doctors/{id}', [AdminDoctorController::class, 'destroy']);

    });
});
