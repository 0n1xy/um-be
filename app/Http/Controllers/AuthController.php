<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
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
