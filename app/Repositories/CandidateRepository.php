<?php

namespace App\Repositories;

use App\Interfaces\CandidateRepositoryInterface;
use App\Models\Candidate;
use Illuminate\Support\Facades\Redis;

class CandidateRepository implements CandidateRepositoryInterface {

    private $model;

    public function __construct(Candidate $model)
    {
        $this->model = $model;
    }

    public function getCandidates()
    {
        $cached = Redis::get('candidates:all');
        if ($cached) {
            return json_decode($cached); // if cache found, return the cache
        }

        $result = $this->model->orderByDesc('id')->get();
        Redis::set('candidates:all', json_encode($result)); // set cache

        return $result;
    }

    public function getPaginateCandidates($limit = null)
    {
        $limit = is_null($limit) ? 10 : $limit; // set 10 as default limit, if not set laravel will set limit 15

        return $this->model->orderByDesc('id')->paginate($limit);
    }

    public function getCandidateById($id)
    {
        $cached = Redis::get('candidates:id:' . $id); // check cached election

        if ($cached) { // if true pass data from cache
            $result = json_decode($cached);
        }
        else {
            $result = $this->model->with(['elections'])->find($id); // get data by id
            Redis::set('candidates:id:' . $id, $result); // set data to cache
        }

        return $result;
    }

    public function getCandidateByElectionId($election_id)
    {
        return $this->model->with(['elections'])->whereElectionId($election_id)->get();
    }

    public function store($data)
    {
        $candidate = $this->model->create($data);

        if ($candidate) { // if created
            Redis::del('candidates:all'); // delete cache candidates
            $this->getCandidates(); // renew cache candidates
            $this->getCandidateById($candidate->id); // set new cache
        }

        return $candidate;
    }

    public function update($data, $id)
    {
        $candidate = $this->model->findOrFail($id);
        $update    = $candidate->update($data);

        if ($update) // if updated
        {
            $cached = Redis::get('candidates:id:' . $id); // check cache

            if ($cached) // if cached
            {
                Redis::del('candidates:id:' . $id); // delete cache from list
                $this->getCandidateById($id); // then set new cache
            }
        }

        return $update;
    }

    public function delete($id)
    {
        $candidate = $this->model->findOrFail($id);
        $delete    = $candidate->delete();

        if ($delete) { // if deleted
            Redis::del('candidates:id:' . $id); // remove cache by id
            Redis::del('candidates:all'); // delete cache candidates
            $this->getCandidates(); // renew cache candidates
        }

        return $delete;
    }
}
