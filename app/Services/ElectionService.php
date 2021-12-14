<?php

namespace App\Services;

use App\Interfaces\ElectionRepositoryInterface;
use App\Traits\ImageTrait;
use Exception;
use InvalidArgumentException;

class ElectionService {

    use ImageTrait;

    private $electionRepository;
    private $path;
    private $thumb;

    public function __construct(ElectionRepositoryInterface $electionRepository)
    {
        $this->electionRepository = $electionRepository;
        $this->path     = "uploads/images/elections/";
        $this->thumb    = "uploads/images/elections/thumb/";
    }

    /**
     * Get All Elections
     *
     * @return collection
     */
    public function getElections()
    {
        try {
            $elections = $this->electionRepository->getElections();
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to retrieve Elections");
        }

        return $elections;
    }

    /**
     * Get Election By ID
     *
     * @param int $id
     * @return array
     */
    public function getElectionById($id)
    {
        try {
            $election = $this->electionRepository->getElectionById($id);

            if (is_null($election))
            {
                throw new Exception("Election ID Not Found");
            }
        }
        catch (\Exception $e) {
            throw new Exception("Election ID Not Found");
        }

        return $election;
    }

    /**
     * Insert new Election
     *
     * @param object $request
     * @return array
     */
    public function store($request)
    {
        $start_date = strtotime($request->start_date);
        $end_date   = strtotime($request->end_date);
        $this->_checkDateElection($start_date, $end_date);

        $countCandidate = count($request->candidate_id);
        $this->_checkTotalCandidates($countCandidate);

        $candidates = [];
        for ($i = 0; $i < $countCandidate; $i++)
        {
            $this->_checkCandidateIsParticipateInElection($request->candidate_id[$i]);
            array_push($candidates, $request->candidate_id[$i]);
        }

        $data = [
            'name'          => $request->name,
            'start_date'    => date('Y-m-d', $start_date),
            'end_date'      => date('Y-m-d', $end_date),
            'image'         => $this->upload($request->image, $this->path, $this->thumb)
        ];

        try {
            $election = $this->electionRepository->store($data, $candidates);
        }
        catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to create new election");
        }

        return $election;
    }

    /**
     * Update Election by ID
     *
     * @param object $request
     * @param id $election_id
     * @return array
     */
    public function update($request, $election_id)
    {
        $this->_checkElectionId($election_id);

        $start_date = strtotime($request->start_date);
        $end_date   = strtotime($request->end_date);
        $this->_checkDateElection($start_date, $end_date);

        $countCandidate = count($request->candidate_id);
        $this->_checkTotalCandidates($countCandidate);

        $candidates = [];
        for ($i = 0; $i < $countCandidate; $i++)
        {
            $this->_checkCandidateIsParticipateInElection($request->candidate_id[$i], $election_id);
            array_push($candidates, $request->candidate_id[$i]);
        }

        $election   = $this->electionRepository->getElectionById($election_id);
        $data       = [
            'name'          => $request->name,
            'start_date'    => date('Y-m-d', $start_date),
            'end_date'      => date('Y-m-d', $end_date)
        ];

        if ($request->hasFile('image'))
        {
            $data['image'] = $this->upload($request->image, $this->path, $this->thumb, $election->image);
        }

        try {
            $result = $this->electionRepository->update($data, $election_id, $candidates);
        }
        catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to update election");
        }

        return $result;
    }

    /**
     * Delete Election by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->_checkElectionId($id);

        try {
            $result = $this->electionRepository->delete($id);
        }
        catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to delete election");
        }

        return $result;
    }

    /**
     * Check Election ID is exist
     *
     * @param int $id
     * @throws exception
     */
    public function _checkElectionId($id)
    {
        $election = $this->electionRepository->getElectionById($id);

        if (!$election)
        {
            throw new Exception("Election ID Not Found");
        }
    }

    /**
     * Check if date diff greater than 3 days
     *
     * @param [date] $start
     * @param [date] $end
     * @throws exception
     */
    public function _checkDateElection($start, $end)
    {
        $diff = ($end - $start) / 60 / 60 / 24;

        if ($diff < 3)
        {
            throw new Exception("End Date must be greater at least 3 days");
        }
    }

    /**
     * Total registered candidate should be greater than equal 2
     *
     * @param int $count
     * @throws exception
     */
    public function _checkTotalCandidates($count)
    {
        if ($count < 2)
        {
            throw new Exception("At least insert 2 candidate to create new Election");
        }
    }

    /**
     * Check is candidate participate on selected election
     *
     * @param int $candidate_id
     * @param int $election_id
     * @throws Exception
     */
    public function _checkCandidateIsParticipateInElection($candidate_id, $election_id = null, $model = "election")
    {
        $result = $this->electionRepository->getElectionByCandidateIdElectionId($candidate_id, $election_id, $model);

        if ($model == "election")
        {
            if (count($result) > 0) // if model election find candidate who still participate in other election
            {
                throw new Exception("Candidate still participate in other on-going election"); // throw error message
            }
        }
        else if ($model == "voting")
        {
            if (count($result) == 0) // if model voting can't find candidate, it mean selected candidate not participate in current election
            {
                throw new Exception("Candidate is not participate in selected Election"); // throw error message
            }
        }
    }
}
