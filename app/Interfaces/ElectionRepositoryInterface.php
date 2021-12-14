<?php

namespace App\Interfaces;

interface ElectionRepositoryInterface {
    public function getElections();
    public function getElectionById($election_id);
    public function getElectionByCandidateIdElectionId($candidate_id, $election_id = null, $model = "election");
    public function store($data, $candidates);
    public function update($data, $election_id, $candidates);
    public function delete($election_id);
}
