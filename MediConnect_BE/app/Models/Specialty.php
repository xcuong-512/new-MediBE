<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $fillable = ['name','slug','description'];

    public function doctors()
    {
        return $this->hasMany(DoctorProfile::class);
    }
}
