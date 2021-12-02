<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->only(['email', 'password']);

        try {
            $result = $this->authService->login($data);

            return $this->success("Login Successfully", 200, $result);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            $result = $this->authService->logout();

            return $this->success("Logout Successfully", 200);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
