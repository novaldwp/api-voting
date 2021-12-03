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
        return $this->model->orderByDesc('end_date')->get();
    }

    public function getElectionById($id)
    {
        return $this->model->find($id);
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function update($data, $id)
    {
        $election = $this->model->findOrFail($id);
        $election->update($data);

        return $election;
    }

    public function delete($id)
    {
        $election = $this->model->findOrFail($id);
        $election->delete();

        return $election;
    }
}
