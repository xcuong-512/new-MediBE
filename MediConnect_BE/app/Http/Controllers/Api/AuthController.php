<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

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
        return ApiResponse::success($request->user(), 'OK');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logout thành công');
    }
}
