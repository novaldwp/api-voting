<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthService {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($data)
    {
        $user = $this->userRepository->getUserByEmail($data['email']);
        $auth = Auth::attempt($data);

        if (!$user || !$auth)
        {
            throw new Exception("Wrong Credentials Invalid Email or Password");

        }

        $data = [
            'name'  => $user->name,
            'email' => simple_encrypt($user->email),
            'role'  => $user->role
        ];

        $result = [
            'token' => Auth::user()->createToken('this is secret key')->plainTextToken,
            'user'  => $data
        ];

        return $result;
    }

    public function logout()
    {
        return auth('sanctum')->user()->tokens()->delete();
    }
}
