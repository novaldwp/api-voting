<?php

namespace App\Interfaces;

interface ElectionRepositoryInterface {
    public function getElections();
    public function getElectionById($id);
    public function store($data);
    public function update($data, $id);
    public function delete($id);
}
