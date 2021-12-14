<?php

namespace App\Repositories;

use App\Interfaces\ElectionRepositoryInterface;
use App\Models\Election;

class ElectionRepository implements ElectionRepositoryInterface {

    private $model;

    public function __construct(Election $model)
    {
        $this->model = $model;
    }

    public function getElections()
    {
        return $this->model->with(['candidates'])->orderByDesc('end_date')->get();
    }

    public function getElectionById($id)
    {
        return $this->model->find($id);
    }

    public function store($data, $candidates)
    {
        $election = $this->model->create($data);
        $election->candidates()->attach($candidates);

        return $election;
    }

    public function update($data, $id, $candidates)
    {
        $election = $this->model->findOrFail($id);
        $election->update($data);
        $election->candidates()->sync($candidates);

        return $election;
    }

    public function delete($id)
    {
        $election = $this->model->findOrFail($id);
        $election->delete();
        $election->candidates()->detach();

        return $election;
    }

    public function getElectionByCandidateIdElectionId($candidate_id, $election_id = null, $model = "election")
    {
        $now        = date('Y-m-d');
        $election   = $this->model->with(['candidates'])
            ->whereHas('candidates', function($q) use($candidate_id, $election_id, $model, $now) {
                $q->when($model == "election", function($q) use($candidate_id, $election_id, $now) { // if model = "election" then find candidate who still participate in on-going election
                    $q->when(!is_null($election_id), function($q) use($election_id) { // if election_id != null then find candidate except current election_id to get result candidate have participate in other election or not
                        $q->where('election_id', '!=', $election_id);
                    });
                    $q->whereCandidateId($candidate_id);
                    $q->where('end_date', '>=', $now);
                });
                $q->when($model == "voting", function($q) use($candidate_id, $election_id) { // if model = "voting" then find match record with candidate_id and election_id
                    $q->whereCandidateId($candidate_id);
                    $q->whereElectionId($election_id);
                });
            })
            ->get();

        return $election;
    }
}
