<?php

namespace App\Interfaces;

interface UserRepositoryInterface {

    public function getUsers($flag);
    public function getPaginateUsers($type, $limit);
    public function getUserById($id);
    public function getUserByEmail($email);
    public function getUserByRole($role);
    public function getUserByEmailAndRole($email, $role);
    public function store($request);
    public function update($request, $id);
    public function delete($id);
}
