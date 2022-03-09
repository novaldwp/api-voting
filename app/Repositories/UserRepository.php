<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class UserRepository implements UserRepositoryInterface {

    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUsers($flag =  null)
    {
        $users = $this->model->when($flag == 1, function($q) {
                $q->where('role', 'admin');
            })
            ->when($flag == 0, function($q) {
                $q->where('role', 'voter');
            })
            ->orderByDesc('id')
            ->get();

        return $users;
    }

    /**
     * Get Data User with Pagination
     *
     * @param string $type 0 = voter, 1 = admin
     * @param int $limit
     * @return collection $result
     */
    public function getPaginateUsers($type, $limit = null)
    {
        $limit  = is_null($limit) ? 10 : $limit;
        $result = $this->model->when($type == 0, function($q) {
                $q->where('role', 'voter');
            })
            ->when($type == 1, function($q) {
                $q->where('role', 'admin');
            })
            ->orderByDesc('id')
            ->paginate($limit);

        return $result;
    }

    public function getUserById($id)
    {
        $cached = Redis::get('users:id:' . $id);

        if ($cached) {
            $result = json_decode($cached); // if cache registered then throw cache
        }
        else {
            $result = $this->model->find($id);
            Redis::set('users:id:' . $id, $result); // store current result to cache
        }

        return $result;
    }

    public function getUserByEmail($email)
    {
        $user = $this->model->whereEmail($email)->first();

        return $user;
    }

    public function getUserByRole($role)
    {
        $user = $this->model->whereRole($role)->first();

        return $user;
    }

    public function getUserByEmailAndRole($email, $role)
    {
        $user = $this->model->whereEmail($email)->whereRole($role)->first();

        return $user;
    }

    public function store($request)
    {
        $user = $this->model->create($request);

        if ($user) { // if created
            $this->getUserById($user->id); // set new cache by id
        }

        return $user;
    }

    public function update($request, $id)
    {
        $result = $this->model->findOrFail($id);
        $update = $result->update($request);

        if ($update) { // if updated
            Redis::del('users:id:' . $id); // delete cache by id
            $this->getUserById($id); // register new cache by id
        }

        return $result;
    }

    public function delete($id)
    {
        $result = $this->model->findOrFail($id);
        $delete = $result->delete();

        if ($delete) { // if deleted
            Redis::del('users:id:' . $id); // delete cache by id
        }

        return $result;
    }
}
