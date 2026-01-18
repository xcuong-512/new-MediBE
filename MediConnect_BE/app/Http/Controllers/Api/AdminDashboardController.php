<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Role;
use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\DoctorSlot;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $roleAdminId   = Role::where('name', 'admin')->value('id');
        $roleDoctorId  = Role::where('name', 'doctor')->value('id');
        $rolePatientId = Role::where('name', 'patient')->value('id');
        $totalUsers = User::count();
        $totalAdmins = $roleAdminId ? User::where('role_id', $roleAdminId)->count() : 0;
        $totalDoctorsUsers = $roleDoctorId ? User::where('role_id', $roleDoctorId)->count() : 0;
        $totalPatients = $rolePatientId ? User::where('role_id', $rolePatientId)->count() : 0;
        $totalDoctorProfiles = DoctorProfile::count();
        $activeDoctorProfiles = DoctorProfile::where('is_active', true)->count();
        $today = Carbon::today()->toDateString();
        $appointmentsToday = Appointment::whereDate('date', $today)->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();
        $slotsToday = DoctorSlot::whereDate('date', $today)->count();
        $slotsAvailableToday = DoctorSlot::whereDate('date', $today)
            ->where('status', 'available')
            ->count();

        $recentAppointments = Appointment::query()
            ->with([
                'patient:id,name,email',
                'doctorProfile.user:id,name,email'
            ])
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'code' => $a->appointment_code,
                    'patient_name' => optional($a->patient)->name,
                    'doctor_name' => optional(optional($a->doctorProfile)->user)->name,
                    'date' => $a->date,
                    'start_time' => $a->start_time,
                    'end_time' => $a->end_time,
                    'type' => $a->type,
                    'status' => $a->status,
                ];
            });

        return ApiResponse::success([
            'stats' => [
                'total_users' => $totalUsers,
                'total_admins' => $totalAdmins,
                'total_patients' => $totalPatients,
                'total_doctors_users' => $totalDoctorsUsers,
                'total_doctor_profiles' => $totalDoctorProfiles,
                'active_doctor_profiles' => $activeDoctorProfiles,
                'appointments_today' => $appointmentsToday,
                'appointments_pending' => $pendingAppointments,
                'appointments_confirmed' => $confirmedAppointments,
                'slots_today' => $slotsToday,
                'slots_available_today' => $slotsAvailableToday,
            ],
            'recent_appointments' => $recentAppointments,
        ], 'OK', 200);
    }
}
