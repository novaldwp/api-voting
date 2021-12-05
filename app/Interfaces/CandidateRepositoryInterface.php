<?php

namespace App\Interfaces;

interface CandidateRepositoryInterface {

    public function getCandidates();
    public function getCandidateById($id);
    public function getCandidateByElectionId($election_id);
    public function store($data);
    public function update($data, $id);
    public function delete($id);
}
