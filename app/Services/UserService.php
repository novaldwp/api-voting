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

    public function getUsers($flag)
    {
        return $this->userRepository->getUsers($flag);
    }

    public function getPaginateUsers($type, $limit)
    {
        $typeString = ($type == 0) ? "Participants":"Users";

        try {
            $result = $this->userRepository->getPaginateUsers($type, $limit);
        } catch (\Exception $e) {
            throw new Exception("Unable to retrieve " . $typeString);
        }

        return $result;
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function getUserByEmail($email)
    {
        try {
            $result = $this->userRepository->getUserByEmail($email);

            if (is_null($result)) {
                $this->_exceptionUserNotFound();
            }
        }
        catch (\Exception $e) {
            $this->_exceptionUserNotFound();
        }

        return $result;
    }

    public function getUserByRole($role)
    {
        try {
            $result = $this->userRepository->getUserByRole($role);

            if (is_null($result)) {
                $this->_exceptionUserNotFound();
            }
        }
        catch (\Exception $e) {
            $this->_exceptionUserNotFound();
        }

        return $result;
    }

    public function getUserByEmailAndRole($email, $role)
    {
        try {
            $result = $this->userRepository->getUserByEmailAndRole($email, $role);

            if (is_null($result)) {
                $this->_exceptionUserNotFound();
            }
        }
        catch (\Exception $e) {
            $this->_exceptionUserNotFound();
        }

        return $result;
    }

    public function store($request)
    {
        $roleString = ($request->role == "voter") ? "Participant":"User";

        $data       = [
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => $request->role
        ];

        try {
            $result = $this->userRepository->store($data);
        }
        catch (\Exception $e) {
            throw new Exception("Invalid to Create new " . $roleString);
        }

        return $result;
    }

    public function update($request, $id)
    {
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ];

        $this->_checkUserId($id);

        try {
            $user = $this->userRepository->update($data, $id);
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
        }
        catch (\Exception $e) {
            Log::info($e->getMessage());

            throw new InvalidArgumentException("Unable to delete user data");
        }

        return $user;
    }

    public function _checkUserId($id)
    {
        $user = $this->userRepository->getUserById($id);

        if (is_null($user))
        {
            $this->_exceptionUserNotFound();
        }
    }

    public function _exceptionUserNotFound()
    {
        throw new Exception("User Data Not Found");
    }
}
