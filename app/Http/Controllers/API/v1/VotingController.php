<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voting\CreateVotingRequest;
use App\Http\Resources\VotingResource;
use App\Services\VotingService;
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
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function paginate(Request $request)
    {
        $limit      = $request->limit;

        try {
            $result = $this->votingService->getPaginateVotings($limit);

            return $this->success("Successfully retrieve votings", 200, VotingResource::collection($result)->response()->getData(true));
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
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
        try {
            $result = $this->votingService->store($request);

            return $this->success("Successfully voting candidate", 201);
        }
         catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
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
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
