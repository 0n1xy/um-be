<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;

// class UserController
// {
//     public function getAllUserData(Request $request)
//     {
//         $users = User::query()
//             ->when($request->query("search"), function ($query, $search) {
//                 $query->where("name", "LIKE", "%$search%")
//                       ->orWhere("email", "LIKE", "%$search%");
//             })
//             ->paginate($request->query("per_page", 20));

//         return response()->json([
//             "users" => $users->map(fn($user) => [
//                 "id" => $user->id,
//                 "name" => $user->name,
//                 "email" => $user->email,
//                 "dateOfBirth" => $user->dateOfBirth,    
//                 "isAdmin" => (int) $user->isAdmin,
//             ]),
//             "total" => $users->total(),
//             "current_page" => $users->currentPage(),
//             "last_page" => $users->lastPage(),
//         ]);
//     }

//     public function getUserById(string $id)
//     {
//         return response()->json(User::findOrFail($id));
//     }

//     public function update(Request $request, $id)
//     {
//         $user = User::findOrFail($id);

//         $validatedData = $request->validate([
//             "name" => "sometimes|string|max:255",
//             "email" => "sometimes|string|email|max:255|unique:users,email,$id",
//             "dateOfBirth" => "sometimes|date",
//             "isAdmin" => "sometimes|boolean",
//             "password" => "sometimes|string|min:6",
//         ]);

//         if (!empty($validatedData["password"])) {
//             $validatedData["password"] = bcrypt($validatedData["password"]);
//         }

//         $user->update($validatedData);

//         return response()->json(["message" => "User updated successfully"]);
//     }

//     public function delete($id)
//     {
//         User::findOrFail($id)->delete();
//         return response()->json(["message" => "User deleted successfully"]);
//     }
// }



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    /**
     * Lấy danh sách người dùng với tìm kiếm và phân trang
     */
    public function getAllUserData(Request $request)
    {
        $users = User::query()
            ->when($request->query("search"), function ($query, $search) {
                $query->where("name", "LIKE", "%$search%")
                      ->orWhere("email", "LIKE", "%$search%");
            })
            ->paginate($request->query("per_page", 20));

        return response()->json([
            "users" => $users->map(fn($user) => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "dateOfBirth" => $user->dateOfBirth,
                "isAdmin" => (int) $user->isAdmin,
            ]),
            "total" => $users->total(),
            "current_page" => $users->currentPage(),
            "last_page" => $users->lastPage(),
        ]);
    }

    /**
     * Lấy thông tin người dùng theo ID
     */
    public function getUserById(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

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
     * Đăng xuất
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token JWT
     */
    public function refresh()
    {
        $user = auth()->user();
        return response()->json([
            'access_token' => auth()->refresh(),
            'refresh_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'role' => $user->isAdmin ? 'admin' : 'user'
        ]);
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

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            "name" => "sometimes|string|max:255",
            "email" => "sometimes|string|email|max:255|unique:users,email,$id",
            "dateOfBirth" => "sometimes|date",
            "isAdmin" => "sometimes|boolean",
            "password" => "sometimes|string|min:6",
        ]);

        if (!empty($validatedData["password"])) {
            $validatedData["password"] = bcrypt($validatedData["password"]);
        }

        $user->update($validatedData);

        return response()->json(["message" => "User updated successfully"]);
    }

    /**
     * Xóa người dùng
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(["message" => "User deleted successfully"]);
    }
}
