<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            \Log::info("Authenticated user:", [$user]); // Debug
            if (!$user) abort(Response::HTTP_UNAUTHORIZED, 'User not found in token');

            // Kiểm tra vai trò nếu middleware yêu cầu
            if ($role === 'admin' && $user->isAdmin != 0) {
                abort(Response::HTTP_FORBIDDEN, 'You do not have admin permission');
            }

            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->refreshTokenAuthentication($request);
        } catch (TokenInvalidException | JWTException $e) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid or missing token');
        }
    }

    private function refreshTokenAuthentication(Request $request): Response
    {
        try {
            $refreshToken = $request->header('Refresh-Token');
            if (!$refreshToken) abort(Response::HTTP_UNAUTHORIZED, 'Refresh token is required');

            $user = JWTAuth::setToken($refreshToken)->authenticate();
            if (!$user) abort(Response::HTTP_UNAUTHORIZED, 'Invalid refresh token');

            return response()->json([
                'access_token' => $this->genAccessToken($user),
                'refresh_token' => $this->genRefreshToken($user),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
        } catch (TokenExpiredException) {
            abort(Response::HTTP_UNAUTHORIZED, 'Refresh token has expired, please log in again');
        } catch (TokenInvalidException | JWTException) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid refresh token');
        }
    }

    private function genAccessToken($user)
    {
        return JWTAuth::claims(["role" => $user->isAdmin == 0 ? "admin" : "user"])
                      ->fromUser($user);
    }

    private function genRefreshToken($user)
    {
        return JWTAuth::fromUser($user);
    }
}
