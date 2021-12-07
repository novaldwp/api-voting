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

    public function getElectionByCandidateId($candidate_id, $election_id = null)
    {
        $now        = date('Y-m-d');
        $election   = $this->model->where('end_date', '>=', $now)
            ->with(['candidates'])
            ->whereHas('candidates', function($q) use($candidate_id, $election_id) {
                $q->when(!is_null($election_id), function($q) use($election_id) {
                    $q->where('election_id', '!=', $election_id);
                });
                $q->where('candidate_id', '=', $candidate_id);
            })
            ->get();

        return $election;
    }
}
