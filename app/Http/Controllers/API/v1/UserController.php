<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userService->getUsers();

        return $this->success("Successfully Retrieve Users", 200, UserResource::collection($users));
    }

    public function paginate(Request $request)
    {
        $type       = $request->type;
        $typeString = ($type == 0) ? "Participants":"Users";
        $limit      = $request->limit;

        try {
            $result = $this->userService->getPaginateUsers($type, $limit);

            return $this->success("Successfully retrieve " . $typeString, 200, UserResource::collection($result)->response()->getData(true));
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $result = $this->userService->store($request);

            return $this->success("Successfully Insert New User", 201);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $result = $this->userService->getUserById($id);

            return $this->success("Successfully Get User", 200, new UserResource($result));
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $result = $this->userService->update($request, $id);

            return $this->success("Successfully Update Current User", 200);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $result = $this->userService->delete($id);

            return $this->success("Successfully Delete User", 200);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
