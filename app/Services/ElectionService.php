<?php

namespace App\Services;

use App\Interfaces\ElectionRepositoryInterface;
use App\Traits\ImageTrait;
use Exception;
use InvalidArgumentException;

class ElectionService {

    use ImageTrait;

    private $electionRepository;
    private $candidateService;
    private $path;
    private $thumb;

    public function __construct(ElectionRepositoryInterface $electionRepository)
    {
        $this->electionRepository = $electionRepository;
        $this->path               = "uploads/images/elections/";
        $this->thumb              = "uploads/images/elections/thumb/";
    }

    /**
     * Get All Elections
     *
     * @return collection
     */
    public function getElections($flag = null)
    {
        try {
            $elections = $this->electionRepository->getElections($flag);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException("Unable to retrieve Elections");
        }

        return $elections;
    }

    public function getElectionWithTotalParticipant()
    {
        try {
            $result = $this->electionRepository->getElectionWithTotalParticipant();

        }
        catch (\Throwable $th) {
            throw new Exception("Unable to get data Elections");
        }

        return $result;
    }

    public function getPaginateElections($limit)
    {
        try {

            $result = $this->electionRepository->getPaginateElections($limit);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get Elections");
        }

        return $result;
    }

    /**
     * Get voting recapitulation by election_id
     *
     * @param int $election_id
     *
     * @return collection
     */
    public function getVotingRecapitulationByElectionId($election_id)
    {
        $this->_checkElectionId($election_id);

        try {
            $votingOverview['candidates']  = [];
            $votingOverview['totalVoting'] = [];
            $data = $this->electionRepository->getVotingRecapitulationByElectionId($election_id);

            foreach($data->candidates as $candidate) {
                array_push($votingOverview['candidates'], $candidate->first_name . ' ' . $candidate->last_name);
                array_push($votingOverview['totalVoting'], $candidate->votings[0]->voting_gain);
            }

            $result = [
                'id'    => $data->id,
                'name'  => $data->name
            ];
            $result['votingOverview'] = $votingOverview;
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get voting recapitulations");
        }

        return $result;
    }

    /**
     * Get Election By ID
     *
     * @param int $id
     * @return array
     */
    public function getElectionById($id)
    {
        $result  = [];
        $user_id = auth()->user()->role == "voter" ? auth()->user()->id : "";

        try {
            $data   = $this->electionRepository->getElectionById($id, $user_id);
            $result = [
                'id'            => $data->id,
                'name'          => $data->name,
                'start_date'    => $data->start_date,
                'end_date'      => $data->end_date,
                'start_datee'   => date('d-m-Y', strtotime($data->start_date)),
                'end_datee'     => date('d-m-Y', strtotime($data->end_date)),
                'thumbnail'     => asset('uploads/images/'. ($data->image == "" ? 'no_image.png':"elections/thumb/".$data->image)),
                'image'         => asset('uploads/images/'. ($data->image == "" ? 'no_image.png':"elections/".$data->image)),
                'candidates_id' => []
            ];

            $candidates     = $data->candidates;

            for($i = 0; $i < count($candidates); $i++) {
                $result['candidates'][$i] = [
                    'id'         => $candidates[$i]->id,
                    'full_name'  => $candidates[$i]->first_name . ' ' . $candidates[$i]->last_name,
                    'vision'     => $candidates[$i]->vision,
                    'mission'    => $candidates[$i]->mission,
                    'thumbnail'  => asset('uploads/images/'. ($candidates[$i]->image == "" ? 'no_image.png':"candidates/thumb/".$candidates[$i]->image)),
                    'image'      => asset('uploads/images/'. ($candidates[$i]->image == "" ? 'no_image.png':"candidates/".$candidates[$i]->image))
                ];

                array_push($result['candidates_id'], $candidates[$i]->id);
            }

            if (count($data->votings) != 0) {
                $result['votings']['id'] = $data->votings[0]->id;
                $result['votings']['candidate_id'] = $data->votings[0]->candidate_id;

            }

            if (is_null($data))
            {
                throw new Exception("Data Election Not Found");
            }
        }
        catch (\Exception $e) {
            throw new Exception("Data Election Not Found");
        }

        return $result;
    }

    /**
     * Insert new Election
     *
     * @param object $request
     *
     * @return array
     */
    public function store($request)
    {
        $start_date = strtotime($request->start_date);
        $end_date   = strtotime($request->end_date);
        $this->_checkDateElection($start_date, $end_date);

        // check if string given, not array
        $request->candidate_id = !is_array($request->candidate_id) ? explode(",", $request->candidate_id) : $request->candidate_id;

        $countCandidate = count($request->candidate_id);
        $this->_checkTotalCandidates($countCandidate);

        $candidates = [];
        for ($i = 0; $i < $countCandidate; $i++)
        {
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
     *
     * @return array
     */
    public function update($request, $election_id)
    {
        $this->_checkElectionId($election_id);

        $election   = $this->electionRepository->getElectionById($election_id);
        $start_date = strtotime($request->start_date);
        $end_date   = strtotime($request->end_date);

        $this->_checkDateElection($start_date, $end_date);
        $this->_checkUpdatingStartDate(strtotime($election->start_date), $start_date);

        // check if string given, not array
        $request->candidate_id = !is_array($request->candidate_id) ? explode(",", $request->candidate_id) : $request->candidate_id;

        $countCandidate = count($request->candidate_id);
        $this->_checkTotalCandidates($countCandidate);

        $candidates = [];
        for ($i = 0; $i < $countCandidate; $i++)
        {
            array_push($candidates, $request->candidate_id[$i]);
        }

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
            $this->deleteImageFromDirectory($result->image, $this->path, $this->thumb);
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
        $diff = ($end - $start) / 60 / 60 / 24; // minute, second, hours

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
     * current start date shouldn't less than old start date
     *
     * @param int $oldStartDate
     * @param int $newStartDate
     * @throws exception
     */
    public function _checkUpdatingStartDate($oldStartDate, $newStartDate)
    {
        if ($oldStartDate > $newStartDate) {
            throw new Exception("New start date can't be less than current start date");
        }
    }
}
