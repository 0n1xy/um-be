<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Exceptions\TokenExpiredException;
// use Tymon\JWTAuth\Exceptions\TokenInvalidException;
// use Tymon\JWTAuth\Exceptions\JWTException;
// use App\Models\User;

// class AuthMiddleware
// {
//     public function handle(Request $request, Closure $next, $role = null): Response
//     {
//         try {
//             $user = JWTAuth::parseToken()->authenticate();

//             if (!$user) {
//                 return response()->json(['message' => 'Unauthorized: User not found in token'], 401);
//             }

//             // 🔹 Truy vấn user từ database để lấy chính xác `isAdmin`
//             $dbUser = User::where('id', $user->id)->first();

//             if (!$dbUser) {
//                 return response()->json(['message' => 'User not found'], 404);
//             }

//             // 🔹 Nếu route yêu cầu quyền admin và user không phải admin
//             if ($dbUser->isAdmin != 0) { // Assuming 1 means admin
//                 return response()->json(['message' => 'Forbidden: You do not have admin permission'], 403);
//             }

//         } catch (TokenExpiredException $e) {
//             return response()->json(['message' => 'Token has expired'], 401);
//         } catch (TokenInvalidException $e) {
//             return response()->json(['message' => 'Token is invalid'], 401);
//         } catch (JWTException $e) {
//             return response()->json(['message' => 'Token is required'], 401);
//         }

//         return $next($request);
//     }
// }

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

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: User not found in token'], 401);
            }

            // 🔹 Truy vấn user từ database để lấy chính xác `isAdmin`
            $dbUser = User::find($user->id);

            if (!$dbUser) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // 🔹 Nếu route yêu cầu quyền admin và user không phải admin
            if ($role === 'admin' && $dbUser->isAdmin != 0) { // 1 là admin
                return response()->json(['message' => 'Forbidden: You do not have admin permission'], 403);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token is required'], 401);
        }

        return $next($request);
    }
}
