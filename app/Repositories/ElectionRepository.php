<?php

namespace App\Repositories;

use App\Interfaces\ElectionRepositoryInterface;
use App\Models\Election;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class ElectionRepository implements ElectionRepositoryInterface {

    private $model;

    public function __construct(Election $model)
    {
        $this->model = $model;
    }

    public function getElections($status = null) // status are => upcoming, ongoing, and finish
    {
        $now    = date('Y-m-d');
        $result = $this->model->when($status == "upcoming", function($q) use($now) {
                $q->where('status', "upcoming");
                $q->where('start_date', '>=', $now);
            })
            ->when($status == "ongoing", function($q) use($now) {
                $q->where('status', "ongoing");
                $q->where('start_date', '<=', $now);
                $q->where('end_date', '>=', $now);
            })
            ->when($status == "finish", function($q) use($now) {
                $q->where('status', "finish");
                $q->where('end_date', '<=', $now);
            })
            ->orderByDesc('end_date')
            ->get();

        return $result;
    }

    public function getElectionWithTotalParticipant()
    {
        $result = $this->model->select('id', 'name')
            ->withCount([
                'votings'
            ])
            ->where('status', '!=', 'upcoming')
            ->orderByDesc('end_date')
            ->limit(5)
            ->get();

        return $result;
    }

    public function getPaginateElections($limit)
    {
        $limit  = is_null($limit) ? 10 : $limit; // set 10 as default limit, if not set laravel will set limit 15
        $result = $this->model->latest()
            ->paginate($limit);

        return $result;
    }

    public function getVotingRecapitulationByElectionId($election_id)
    {
        $result = $this->model->select('id', 'name')
            ->with([
                'candidates' => function($q) {
                    $q->select(DB::raw('candidates.id'), 'first_name', 'last_name');
                },
                'candidates.votings' => function($q) use($election_id){
                    $q->where('election_id', $election_id);
                    $q->select('id', 'candidate_id', 'election_id', DB::Raw('count(candidate_id) as voting_gain'));
                    $q->groupBy('election_id', 'candidate_id');
                    $q->orderByDesc('voting_gain');
                }
            ])
            ->whereId($election_id)
            ->first();

        return $result;
    }

    public function getElectionById($id, $user_id = null)
    {
        $result = $this->model->with('candidates')
            ->when($user_id != "", function($q) use($user_id) {
                $q->with('votings', function($q) use($user_id) {
                    $q->whereUserId($user_id);
                });
            })
            ->whereId($id)
            ->first();

        return $result;
    }

    public function store($data, $candidates)
    {
        $election = $this->model->create($data);
        $election->candidates()->attach($candidates);

        if ($election) { // if created
            $this->getElectionById($election->id); // set new cache by id
        }

        return $election;
    }

    public function update($data, $id, $candidates)
    {
        $election = $this->model->findOrFail($id);
        $update   = $election->update($data);
        $election->candidates()->sync($candidates);

        if ($update) { // if update
            $cached = Redis::get('elections:id:' . $id); // get cache by id

            if ($cached) { // if cached
                Redis::del('elections:id:' . $id); // delete cache by id
                $election = $this->getElectionById($id); // set new cache by id

                for ($i = 0; $i < count($election->candidates); $i++) {
                    Redis::del('candidates:id:' . $election->candidates[$i]->id); // remove cache candidates by id, so detail candidate can be updated
                }
            }
        }

        return $update;
    }

    public function delete($id)
    {
        $election = $this->model->findOrFail($id);
        $election->delete();
        $election->candidates()->detach();

        if ($election) { // if deleted
            Redis::del('elections:id:' . $id); // delete cache by id
        }

        return $election;
    }
}
