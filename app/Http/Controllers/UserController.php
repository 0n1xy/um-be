<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Services\UserService;

class UserController
{   
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // public function getAllUserData() {
        
    //     return response()->json(User::all(), 200);
    // }

public function getAllUserData(Request $request)
{
    $search = $request->query('search', ''); // Lấy tham số tìm kiếm
    $perPage = $request->query('per_page', 20); // Số user mỗi trang (mặc định 20)

    $query = User::query();

    if (!empty($search)) {
        $query->where('name', 'LIKE', "%$search%")
              ->orWhere('email', 'LIKE', "%$search%");
    }

    // Phân trang
    $users = $query->paginate($perPage);

    return response()->json([
        'users' => $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'dateOfBirth' => $user->dateOfBirth,
                'isAdmin' => (int) $user->isAdmin, // Chuyển thành số nguyên (0 hoặc 1)
            ];
        }),
        'total' => $users->total(),
        'current_page' => $users->currentPage(),
        'last_page' => $users->lastPage(),
    ]);
}

    public function getUserById(string $id) {
        if ($id == null) { 
            return response()->json(["message" => "Id is required"], 400); 
        }
        
        $user = User::find($id);

        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }
        
        return response()->json($user, 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }

        $validatedData = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
        'dateOfBirth' => 'sometimes|date',
        'isAdmin' => 'sometimes|boolean',
        'password' => 'sometimes|string|min:6',
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json(["message" => "User updated successfully"]);
    }

    // public function update(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->update($request->all());

    //     return response()->json(['message' => 'User updated successfully']);
    // }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }

        $user->delete();
            return response()->json(["message" => "User deleted successfully"]);
    }

    // public function delete($id)
    // {
    //     User::findOrFail($id)->delete();
    //     return response()->json(['message' => 'User deleted successfully']);
    // }
    
    public function register(Request $request)
    {
        return $this->userService->register($request);
    }

    public function login(Request $request)
    {
        return $this->userService->login($request);
    }

    public function me()
    {
        return $this->userService->me();
    }

    public function logout()
    {
        return $this->userService->logout();
    }

    public function refresh()
    {
        return $this->userService->refresh();
    }
}