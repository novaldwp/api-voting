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
        $data = [
            'name'          => $request->name,
            'start_date'    => date('Y-m-d', strtotime($request->start_date)),
            'end_date'      => date('Y-m-d', strtotime($request->end_date)),
            'image'         => $this->upload($request->image, $this->path, $this->thumb)
        ];

        try {
            $election = $this->electionRepository->store($data);
        }
        catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to create new election");
        }

        return $election;
    }

    public function update($request, $id)
    {
        $this->_checkElectionId($id);

        $election   = $this->electionRepository->getElectionById($id);
        $data       = [
            'name'          => $request->name,
            'start_date'    => date('Y-m-d', strtotime($request->start_date)),
            'end_date'      => date('Y-m-d', strtotime($request->end_date)),
            !$request->hasFile('image') ?: 'image' => $this->upload($request->image, $this->path, $this->thumb, $election->image)
        ];

        try {
            $result = $this->electionRepository->update($data, $id);
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

        return $election;
    }
}
