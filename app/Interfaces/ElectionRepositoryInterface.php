<?php

namespace App\Interfaces;

interface ElectionRepositoryInterface {
    public function getElections();
    public function getElectionWithTotalParticipant();
    public function getVotingRecapitulationByElectionId($election_id);
    public function getPaginateElections($limit);
    public function getElectionById($election_id, $user_id = null);
    public function store($data, $candidates);
    public function update($data, $election_id, $candidates);
    public function delete($election_id);
}
