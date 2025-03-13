<?php

// namespace App\Services;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Tymon\JWTAuth\Facades\JWTAuth;

// class UserService
// {
//     public function register(Request $request)
//     {
//         try {
//             // Validate input
//             $validatedData = $request->validate([
//                 "name" => "required|string|max:255",
//                 "email" => "required|string|email|max:255|unique:users,email",
//                 "password" => "required|string|min:6",
//             ]);

//             // Tạo user sau khi validate thành công
//             $user = User::create([
//                 "name" => $validatedData["name"],
//                 "email" => $validatedData["email"],
//                 "password" => Hash::make($validatedData["password"]),
//             ]);

//             return response()->json(
//                 [
//                     "message" => "User registered successfully",
//                     "user" => $user,
//                 ],
//                 201
//             );
//         } catch (ValidationException $e) {
//             return response()->json(
//                 [
//                     "message" => "Validation failed",
//                     "errors" => $e->errors(),
//                 ],
//                 422
//             );
//         } catch (QueryException $e) {
//             // Kiểm tra lỗi ràng buộc UNIQUE trên email
//             if ($e->errorInfo[1] == 1062) {
//                 return response()->json(
//                     [
//                         "message" => "Email đã tồn tại",
//                     ],
//                     409
//                 ); // 409 Conflict
//             }

//             return response()->json(
//                 [
//                     "message" => "Database error",
//                     "error" => $e->getMessage(),
//                 ],
//                 500
//             );
//         }
//     }

//     public function login(Request $request)
//     {
//         $credentials = $request->only("email", "password");

//         if (!($token = JWTAuth::attempt($credentials))) {
//             return response()->json(["error" => "Unauthorized"], 401);
//         }

//         $user = auth()->user();

//         $customClaims = ["role" => $user->isAdmin == 0 ? "admin" : "user"];
//         $accessToken = JWTAuth::claims($customClaims)->fromUser($user);

//         // Tạo refresh token
//         $refreshToken = JWTAuth::fromUser($user);

//         return response()->json([
//             "access_token" => $accessToken,
//             "refresh_token" => $refreshToken,
//             "token_type" => "bearer",
//             "expires_in" =>
//                 auth()
//                     ->factory()
//                     ->getTTL() * 60,
//             "role" => $user->isAdmin ? "user" : "admin",
//         ]);
//     }

//     /**
//      * Lấy thông tin người dùng hiện tại
//      */
//     public function me()
//     {
//         return response()->json(auth()->user());
//     }

//     /**
//      * Đăng xuất
//      */
//     public function logout()
//     {
//         auth()->logout();
//         return response()->json(["message" => "Successfully logged out"]);
//     }

//     public function refresh()
//     {
//         return $this->respondWithToken(auth()->refresh());
//     }

//     /**
//      * Trả về Access Token và thông tin
//      */
//     protected function respondWithToken($token)
//     {
//         return response()->json([
//             "access_token" => $accessToken,
//             "refresh_token" => $refreshToken,
//             "token_type" => "bearer",
//             "expires_in" =>
//                 auth()
//                     ->factory()
//                     ->getTTL() * 60, // Thời gian hết hạn của access token (giây)
//             "role" => $user->isAdmin ? "admin" : "user", // Trả về role trực tiếp nếu cần
//         ]);
//     }
// }


namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users,email",
            "password" => "required|string|min:6",
        ]);

        $user = User::create([
            "name" => $validatedData["name"],
            "email" => $validatedData["email"],
            "password" => bcrypt($validatedData["password"]),
        ]);

        return response()->json([
            "message" => "User registered successfully",
            "user" => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only("email", "password");

        if (!($token = JWTAuth::attempt($credentials))) {
            abort(401, "Unauthorized");
        }

        $user = auth()->user();

        return $this->respondWithToken(
            $this->genAccessToken($user),
            $this->genRefreshToken($user),
            $user
        );
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

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(["message" => "Successfully logged out"]);
    }

    public function refresh()
    {
        $user = auth()->user();
        return $this->respondWithToken(
            auth()->refresh(),
            JWTAuth::fromUser($user),
            $user
        );
    }

    protected function respondWithToken($accessToken, $refreshToken, $user)
    {
        return response()->json([
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "token_type" => "bearer",
            "expires_in" => auth()->factory()->getTTL() * 60,
            "role" => $user->isAdmin == 0 ? "admin" : "user",
        ]);
    }
}

