<?php

namespace App\Interfaces;

interface VotingRepositoryInterface {

    public function getVotings();
    public function getVotingById($voting_id);
    public function getVotingByCandidateId($candidate_id);
    public function getVotingByElectionId($election_id);
    public function getVotingByElectionIdUserId($election_id, $user_id);
    public function getVotingByCandidateIdElectionId($candidate_id, $election_id);
    public function store($data);
    public function update($data, $voting_id);
    public function delete($voting_id);
}
