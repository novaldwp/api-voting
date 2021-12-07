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

    public function getElections()
    {
        $elections = $this->electionRepository->getElections();

        return $elections;
    }

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
            $this->_checkCandidateIsStillParticipateElection($request->candidate_id[$i]);
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
            $this->_checkCandidateIsStillParticipateElection($request->candidate_id[$i], $election_id);
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

    public function _checkElectionId($id)
    {
        $election = $this->electionRepository->getElectionById($id);

        if (!$election)
        {
            throw new Exception("Election ID Not Found");
        }
    }

    public function _checkDateElection($start, $end)
    {
        $diff = ($end - $start) / 60 / 60 / 24;

        if ($diff < 3)
        {
            throw new Exception("End Date must be greater at least 3 days");
        }
    }

    public function _checkTotalCandidates($count)
    {
        if ($count < 2)
        {
            throw new Exception("At least insert 2 candidate to create new Election");
        }
    }

    public function _checkCandidateIsStillParticipateElection($candidate_id, $election_id = null)
    {
        $result = $this->electionRepository->getElectionByCandidateId($candidate_id, $election_id);

        if (count($result) != 0)
        {
            throw new Exception("Candidate still participate in other election");
        }
    }
}
