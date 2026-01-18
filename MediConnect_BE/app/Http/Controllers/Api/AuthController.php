<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return ApiResponse::error('Email hoặc mật khẩu không đúng', null, 401);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Tài khoản đang bị khóa'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => optional($user->role)->name,
        ], 'Login thành công', 200);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role'); 
        return ApiResponse::success($user, 'OK');
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logout thành công');
    }
    
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $patientRole = Role::where('name', 'patient')->first();

        if (!$patientRole) {
            return ApiResponse::error('Role patient chưa tồn tại, hãy seed RoleSeeder', null, 500);
        }

        $user = User::create([
            'role_id' => $patientRole->id,
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role'),
            'role' => optional($user->role)->name,
        ], 'Register thành công', 201);
    }
}
