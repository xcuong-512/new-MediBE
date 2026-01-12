<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatusHistory extends Model
{
    protected $fillable = [
        'appointment_id',
        'changed_by_user_id',
        'from_status',
        'to_status',
        'note',
    ];
}
