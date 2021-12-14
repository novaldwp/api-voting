<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voting\CreateVotingRequest;
use App\Http\Requests\Voting\UpdateVotingRequest;
use App\Http\Resources\VotingResource;
use App\Services\VotingService;
use Error;
use Exception;
use Illuminate\Http\Request;

class VotingController extends Controller
{
    private $votingService;

    public function __construct(VotingService $votingService)
    {
        $this->votingService = $votingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $result = $this->votingService->getVotings();

            return $this->success("Successfully retrieve votings", 200, VotingResource::collection($result));
        }
        catch (Exception $e) {
            return $this->error($e->getMessage(), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVotingRequest $request)
    {
        $result = $this->votingService->store($request);
        return $result;
        try {
            $result = $this->votingService->store($request);

            return $this->success("Successfully insert new voting", 201, new VotingResource($result));
        }
         catch (Exception $e) {
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
            $result = $this->votingService->getVotingById($id);

            return $this->success("Successfully get voting", 200, new VotingResource($result));
        }
        catch (Exception $e) {
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
    public function update(UpdateVotingRequest $request, $id)
    {
        try {
            $result = $this->votingService->update($request, $id);

            return $this->success("Successfully update voting", 200, new VotingResource($result));
        }
        catch (Exception $e) {
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
            $result = $this->votingService->delete($id);

            return $this->success("Successfully delete voting", 200);
        }
        catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
