<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface {

    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUsers()
    {
        $users = $this->model->orderByDesc('id')->get();

        return $users;
    }

    public function getUserById($id)
    {
        $user = $this->model->find($id);

        return $user;
    }

    public function getUserByEmail($email)
    {
        $user = $this->model->whereEmail($email)->first();

        return $user;
    }

    public function store($request)
    {
        $user = $this->model->create($request);

        return $user;
    }

    public function update($request, $id)
    {
        $user = $this->model->findOrFail($id);
        $user->update($request);

        return $user;
    }

    public function delete($id)
    {
        $user = $this->model->findOrFail($id);
        $user->delete();

        return $user;
    }
}
