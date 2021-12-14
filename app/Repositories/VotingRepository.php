<?php


namespace App\Repositories;

use App\Interfaces\VotingRepositoryInterface;
use App\Models\Voting;

class VotingRepository implements VotingRepositoryInterface {

    private $model;

    public function __construct(Voting $model)
    {
        $this->model = $model;
    }

    public function getVotings()
    {
        return $this->model->orderByDesc('id')->get();
    }

    public function getVotingById($voting_id)
    {
        return $this->model->find($voting_id);
    }

    public function getVotingByCandidateId($candidate_id)
    {
        return $this->model->whereCandidateId($candidate_id)->get();
    }

    public function getVotingByElectionid($election_id)
    {
        return $this->model->whereElectionId($election_id)->get();
    }

    public function getVotingByElectionIdUserId($election_id, $user_id)
    {
        return $this->model->whereElectionId($election_id)->whereUserId($user_id)->first();
    }

    public function getVotingByCandidateIdElectionId($candidate_id, $election_id)
    {
        return $this->model->whereCandidateId($candidate_id)->whereElectionId($election_id)->first();
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function update($data, $voting_id)
    {
        $voting = $this->model->findOrFail($voting_id);
        $voting->update($data);

        return $voting;
    }

    public function delete($voting_id)
    {
        $voting = $this->model->findOrFail($voting_id);

        return $voting;
    }
}
