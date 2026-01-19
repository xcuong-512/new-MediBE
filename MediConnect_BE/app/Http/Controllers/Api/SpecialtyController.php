<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $specialties
        ]);
    }
}
