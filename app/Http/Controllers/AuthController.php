<?php

namespace App\Http\Controllers;

use \App\Services\UserService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
class AuthController
{
    /**
     * Đăng ký tài khoản
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Email đã tồn tại'
                ], 409);
            }
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đăng nhập và tạo JWT token
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'access_token' => $this->genAccessToken($user),
            'refresh_token' => $this->genRefreshToken($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'role' => $user->isAdmin ? 'user' : 'admin' 
        ]);
    }

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Tạo Access Token với claims
     */
    private function genAccessToken($user)
    {
        return JWTAuth::claims(['role' => $user->isAdmin == 0 ? 'admin' : 'user'])
                      ->fromUser($user);
    }

    /**
     * Tạo Refresh Token
     */
    private function genRefreshToken($user)
    {
        return JWTAuth::fromUser($user);
    }

}
