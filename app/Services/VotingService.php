<?php

namespace App\Services;

use App\Interfaces\VotingRepositoryInterface;
use Exception;
use InvalidArgumentException;

class VotingService {

    private $votingRepository;
    private $candidateService;
    private $electionService;

    public function __construct(VotingRepositoryInterface $votingRepository, CandidateService $candidateService, ElectionService $electionService)
    {
        $this->votingRepository = $votingRepository;
        $this->candidateService = $candidateService;
        $this->electionService  = $electionService;
    }

    public function getVotings()
    {
        try {
            $result = $this->votingRepository->getVotings();
        }
        catch (Exception $e) {
            throw new Exception("Unable to get data votings", 500);
        }

        return $result;
    }

    public function getPaginateVotings($limit)
    {
        $user_id = auth()->user()->id;
        $flag    = auth()->user()->role;

        try {
            $result = $this->votingRepository->getPaginateVotings($limit, $flag, $user_id);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get data votings", 500);
        }

        return $result;
    }

    public function getVotingById($voting_id)
    {
        try {
            $result = $this->votingRepository->getVotingById($voting_id);
        }
        catch (\Exception $e) {
            throw new Exception("Data Voting Not Found", 404);
        }

        return $result;
    }

    public function getVotingByCandidateId($candidate_id)
    {
        try {
            $result = $this->votingRepository->getVotingByCandidateId($candidate_id);
        }
        catch (Exception $e) {
            throw new Exception("Data Candidate Not Found", 404);
        }

        return $result;
    }

    public function getVotingByElectionId($election_id)
    {
        try {
            $result = $this->votingRepository->getVotingByElectionId($election_id);
        }
        catch (Exception $e) {
            throw new Exception("Data Election Not Found", 404);
        }

        return $result;
    }

    public function getVotingByCandidateIdElectionId($candidate_id, $election_id)
    {
        try {
            $result = $this->votingRepository->getVotingByCandidateIdElectionId($candidate_id, $election_id);
        }
        catch (Exception $e) {
            throw new Exception("Data Candidate or Election Not Found", 404);
        }

        return $result;
    }

    public function getVotingByElectionidUserId($election_id, $user_id)
    {
        try {
            $result = $this->votingRepository->getVotingByElectionidUserId($election_id, $user_id);
        }
        catch (\Exception $e) {
            throw new Exception("Data Voting Not Found.", 404);
        }

        return $result;
    }

    public function store($request)
    {
        // get role user
        $user_id        = auth()->user()->id;
        $candidate_id   = $request->candidate_id;
        $election_id    = $request->election_id;

        $this->candidateService->_checkCandidateId($candidate_id); // validate candidate id
        $this->electionService->_checkElectionId($election_id); // validate election id

        $voting = $this->votingRepository->getVotingByElectionIdUserId($election_id, $user_id); // check if user already voting in current election

        if ($voting) {
            throw new Exception("You Only Can Vote Once on Every Election.", 400);
        }

        $data   = [
            'user_id'       => $user_id,
            'candidate_id'  => $candidate_id,
            'election_id'   => $election_id
        ];

        try {
            $result = $this->votingRepository->store($data);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to insert voting", 500);
        }

        return $result;
    }
}
