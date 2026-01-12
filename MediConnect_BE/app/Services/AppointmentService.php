<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;

class AppointmentService
{
    public static function logStatus(
        Appointment $appointment,
        ?int $changedByUserId,
        ?string $from,
        string $to,
        ?string $note = null
    ): void {
        AppointmentStatusHistory::create([
            'appointment_id' => $appointment->id,
            'changed_by_user_id' => $changedByUserId,
            'from_status' => $from,
            'to_status' => $to,
            'note' => $note,
        ]);
    }
}
