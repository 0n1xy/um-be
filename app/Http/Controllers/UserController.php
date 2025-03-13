<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    /**
     * Lấy danh sách người dùng với tìm kiếm và phân trang
     */
    public function getAllUserData(Request $request)
{
    // Lấy số user mỗi trang (default: 20, max: 50)
    $perPage = (int) $request->query("per_page", 20);
    $perPage = $perPage > 50 ? 50 : $perPage; // Giới hạn tối đa 50 user/trang

    $users = User::query()
        ->when($request->query("search"), function ($query, $search) {
            $query->where("name", "LIKE", "%$search%")
                  ->orWhere("email", "LIKE", "%$search%");
        })
        ->paginate($perPage);

    return response()->json([
        "users" => $users->map(fn($user) => [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "dateOfBirth" => $user->dateOfBirth ? $user->dateOfBirth->format("Y-m-d") : null, // Format đúng
            "isAdmin" => (int) $user->isAdmin, // Ép thành số nguyên
        ]),
        "total_users" => $users->total(), // Tổng số user
        "current_page" => $users->currentPage(), // Trang hiện tại
        "total_pages" => $users->lastPage(), // Tổng số trang
        "per_page" => $users->perPage(), // Số user mỗi trang
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
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            "name" => "sometimes|string|max:255",   
            "email" => "sometimes|string|email|max:255|unique:users,email,$id",
            "dateOfBirth" => "sometimes|date", 
            "isAdmin" => "sometimes|integer", 
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
