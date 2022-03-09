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

    public function getPaginateVotings($limit, $flag, $user_id = null)
    {
        $limit = !is_null($limit) ? $limit : 10; // set default limiter

        $result = $this->model->when($flag == "voter", function($q) use($user_id) {
                $q->whereUserId($user_id);
            })
            ->with([
                'elections' => function($q) {
                    $q->select('id', 'name');
                },
                'users' => function($q) {
                    $q->select('id', 'name', 'email');
                },
                'candidates' => function($q) {
                    $q->select('id', 'first_name', 'last_name');
                }
            ])
            ->latest()
            ->paginate($limit);

        return $result;
    }

    public function getVotingById($voting_id)
    {
        $result = $this->model->find($voting_id);

        return $result;
    }

    public function getVotingByCandidateId($candidate_id)
    {
        $result = $this->model->whereCandidateId($candidate_id)->get();

        return $result;
    }

    public function getVotingByElectionid($election_id)
    {
        $result = $this->model->whereElectionId($election_id)->get();

        return $result;
    }

    public function getVotingByElectionIdUserId($election_id, $user_id)
    {
        $result = $this->model->whereElectionId($election_id)->whereUserId($user_id)->first();

        return $result;
    }

    public function getVotingByCandidateIdElectionId($candidate_id, $election_id)
    {
        $result = $this->model->whereCandidateId($candidate_id)->whereElectionId($election_id)->first();

        return $result;
    }

    public function store($data)
    {
        $result = $this->model->create($data);

        return $result;
    }

    public function update($data, $voting_id)
    {
        $result = $this->model->findOrFail($voting_id);
        $result->update($data);

        return $result;
    }

    public function delete($voting_id)
    {
        $result = $this->model->findOrFail($voting_id);

        return $result;
    }
}
