<?php

namespace App\Services;

use App\Interfaces\CandidateRepositoryInterface;
use Exception;

class CandidateService {

    private $candidateRepository;
    private $electionService;

    public function __construct(CandidateRepositoryInterface $candidateRepository, ElectionService $electionService)
    {
        $this->candidateRepository = $candidateRepository;
        $this->electionService = $electionService;
    }

    public function getCandidates()
    {
        try {
            $result = $this->candidateRepository->getCandidates();
        } catch (\Exception $e) {
            throw new Exception("Unable to get Candidates");
        }

        return $result;
    }

    public function getCandidateById($id)
    {
        try {
            $result = $this->candidateRepository->getCandidateById($id);
        }
        catch (\Exception $e) {
            throw new Exception("Candidate ID Not Found");
        }

        return $result;
    }

    public function getCandidateByElectionId($election_id)
    {
        try {
            $result = $this->candidateRepository->getCandidateByElectionId($election_id);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get candidates by election");
        }

        return $result;
    }

    public function store($request)
    {
        $election_id = $request->election_id;
        $this->electionService->_checkElectionId($election_id);

        $data = [
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'dob'           => date('Y-m-d', strtotime($request->dob)),
            'address'       => strip_tags($request->address),
            'vision'        => $request->vision,
            'mission'       => $request->mission
        ];

        try {
            $result = $this->candidateRepository->store($data);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to create new candidate");
        }

        return $result;
    }

    public function update($request, $id)
    {
        $election_id = $request->election_id;
        $this->_checkCandidateId($id);
        $this->electionService->_checkElectionId($election_id);

        $data = [
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'dob'           => date('Y-m-d', strtotime($request->dob)),
            'address'       => strip_tags($request->address),
            'vision'        => $request->vision,
            'mission'       => $request->mission
        ];
        try {
            $result = $this->candidateRepository->update($data, $id);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to update data");
        }

        return $result;
    }

    public function delete($id)
    {
        $this->_checkCandidateId($id);

        try {
            $result = $this->candidateRepository->delete($id);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to delete data");
        }

        return $result;
    }

    public function _checkCandidateId($id)
    {
        $candidate = $this->candidateRepository->getCandidateById($id);

        if (!$candidate)
        {
            throw new Exception("Candidate ID Not Found");
        }
    }

}
