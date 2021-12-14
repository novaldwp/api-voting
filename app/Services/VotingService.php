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
            throw new Exception("Unable to get votings data");
        }

        return $result;
    }

    public function getVotingById($voting_id)
    {
        try {
            $result = $this->votingRepository->getVotingById($voting_id);
        }
        catch (\Exception $e) {
            throw new Exception("Voting ID Not Found");
        }

        return $result;
    }

    public function getVotingByCandidateId($candidate_id)
    {
        try {
            $result = $this->votingRepository->getVotingByCandidateId($candidate_id);
        }
        catch (Exception $e) {
            throw new Exception("Candidate ID Not Found");
        }

        return $result;
    }

    public function getVotingByElectionId($election_id)
    {
        try {
            $result = $this->votingRepository->getVotingByElectionId($election_id);
        }
        catch (Exception $e) {
            throw new Exception("Election ID Not Found");
        }

        return $result;
    }

    public function getVotingByCandidateIdElectionId($candidate_id, $election_id)
    {
        try {
            $result = $this->votingRepository->getVotingByCandidateIdElectionId($candidate_id, $election_id);
        }
        catch (Exception $e) {
            throw new Exception("Candidate or Election ID Not Found");
        }

        return $result;
    }

    public function store($request)
    {
        // get role user
        $user_id        = $request->user_id;
        $candidate_id   = $request->candidate_id;
        $election_id    = $request->election_id;

        $this->candidateService->_checkCandidateId($candidate_id); // validate candidate id
        $this->electionService->_checkElectionId($election_id); // validate election id
        $this->electionService->_checkCandidateIsParticipateInElection($candidate_id, $election_id, "voting"); // validate candidate who participate in election

        $voting = $this->votingRepository->getVotingByElectionIdUserId($election_id, $user_id); // check if user already voting in current election
        $data   = [
            'user_id'       => $user_id,
            'candidate_id'  => $candidate_id,
            'election_id'   => $election_id
        ];

        if ($voting) // if yes
        {
            // unset user_id and election_id
            unset($data['user_id']);
            unset($data['election_id']);

            return $this->update($data, $voting->id); // update candidate_id with voting_id
        }

        try {
            $result = $this->votingRepository->store($data); // if no then store $data
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to insert voting");
        }

        return $result;
    }

    public function update($data, $voting_id)
    {
        try {
            $result = $this->votingRepository->update($data, $voting_id);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to update voting");
        }

        return $result;
    }

    public function delete($voting_id)
    {
        try {
            $result = $this->votingRepository->delete($voting_id);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to delete voting");
        }

        return $result;
    }
}
