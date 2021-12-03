<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UserService {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers()
    {
        return $this->userRepository->getUsers();
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function store($request)
    {
        return $this->userRepository->store($request);
    }

    public function update($request, $id)
    {
        if ($id != auth()->user()->id)
        {
            throw new Exception("Access Forbidden");
        }

        $this->_checkUserId($id);

        try {
            $user = $this->userRepository->update($request, $id);
        }
        catch (\Exception $e) {
            Log::info($e->getMessage());

            throw new InvalidArgumentException("Unable to update user data");
        }

        return $user;
    }

    public function delete($id)
    {
        $this->_checkUserId($id);

        try {
            $user = $this->userRepository->delete($id);
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            throw new InvalidArgumentException("Unable to delete user data");
        }

        return $user;
    }

    public function _checkUserId($id)
    {
        $user = $this->userRepository->getUserById($id);

        if (!$user)
        {
            throw new Exception("User ID not found");
        }
    }
}
