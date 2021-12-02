<?php

namespace App\Interfaces;

interface UserRepositoryInterface {

    public function getUsers();
    public function getUserById($id);
    public function getUserByEmail($email);
    public function store($request);
    public function update($request, $id);
    public function delete($id);
}
