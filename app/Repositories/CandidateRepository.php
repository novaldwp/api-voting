<?php

namespace App\Repositories;

use App\Interfaces\CandidateRepositoryInterface;
use App\Models\Candidate;

class CandidateRepository implements CandidateRepositoryInterface {

    private $model;

    public function __construct(Candidate $model)
    {
        $this->model = $model;
    }

    public function getCandidates()
    {
        return $this->model->with(['elections'])->orderByDesc('id')->get();
    }

    public function getCandidateById($id)
    {
        return $this->model->with(['elections'])->find($id);
    }

    public function getCandidateByElectionId($election_id)
    {
        return $this->model->with(['elections'])->whereElectionId($election_id)->get();
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function update($data, $id)
    {
        $candidate = $this->model->findOrFail($id);
        $candidate->update($data);

        return $candidate;
    }

    public function delete($id)
    {
        $candidate = $this->model->findOrFail($id);
        $candidate->delete();

        return $candidate;
    }
}
