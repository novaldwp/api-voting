<?php

namespace App\Interfaces;

interface ElectionRepositoryInterface {
    public function getElections();
    public function getElectionById($id);
    public function getElectionByCandidateId($candidate_id, $id);
    public function store($data, $candidates);
    public function update($data, $id, $candidates);
    public function delete($id);
}
