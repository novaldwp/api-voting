<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\CreateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    private $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $result = $this->candidateService->getCandidates();

            return $this->success("Successfully retrieve candidates", 200, CandidateResource::collection($result));
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Display a listing of the resource using paginate
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $limit = $request->limit; // limit per page

        try {
            $result = $this->candidateService->getPaginateCandidates($limit);

            return $this->success("Successfully retrieve candidates", 200, CandidateResource::collection($result)->response()->getData(true));
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
    public function store(CreateCandidateRequest $request)
    {
        try {
            $result = $this->candidateService->store($request);

            return $this->success("Successfully insert new candidate", 201);
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
            $result = $this->candidateService->getCandidateById($id);

            return $this->success("Successfully get candidate", 200, $result);
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
    public function update(Request $request, $id)
    {
        try {
            $result = $this->candidateService->update($request, $id);

            return $this->success("Successfully update candidate", 200);
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
            $result = $this->candidateService->delete($id);

            return $this->success("Successfully delete candidate", 200);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
