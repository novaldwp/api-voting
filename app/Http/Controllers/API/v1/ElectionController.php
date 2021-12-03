<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Election\CreateElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Http\Resources\ElectionResource;
use App\Services\ElectionService;

class ElectionController extends Controller
{
    private $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $result = $this->electionService->getElections();

            return $this->success("Successfully retrieve elections", 200, ElectionResource::collection($result));
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
    public function store(CreateElectionRequest $request)
    {
        try {
            $result = $this->electionService->store($request);

            return $this->success("Successfully Insert New Election", 201, new ElectionResource($result));
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
        // return $this->electionService->getElectionById($id);
        try {
            $result = $this->electionService->getElectionById($id);

            return $this->success("Successfully Get Election", 200, $result);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateElectionRequest $request, $id)
    {
        try {
            $result = $this->electionService->update($request, $id);

            return $this->success("Successfully update election", 200, $result);
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
            $result = $this->electionService->delete($id);

            return $this->success("Successfully delete election", 200);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
