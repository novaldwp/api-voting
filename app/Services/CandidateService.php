<?php

namespace App\Services;

use App\Interfaces\CandidateRepositoryInterface;
use App\Traits\ImageTrait;
use Exception;

class CandidateService {

    use ImageTrait;

    private $candidateRepository;
    private $electionService;
    private $path;
    private $thumb;

    public function __construct(CandidateRepositoryInterface $candidateRepository, ElectionService $electionService)
    {
        $this->path                 = "uploads/images/candidates/";
        $this->thumb                = "uploads/images/candidates/thumb/";
        $this->candidateRepository  = $candidateRepository;
        $this->electionService = $electionService;
    }

    public function getCandidates()
    {
        try {
            $result = $this->candidateRepository->getCandidates();
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get Candidates");
        }

        return $result;
    }

    public function getPaginateCandidates($limit)
    {
        try {

            $result = $this->candidateRepository->getPaginateCandidates($limit);
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get Candidates");
        }

        return $result;
    }

    public function getCandidateById($id)
    {
        $result = [];

        try {
            $data   = $this->candidateRepository->getCandidateById($id);
            $result = [
                'id'         => $data->id,
                'first_name' => $data->first_name,
                'last_name'  => $data->last_name,
                'full_name'  => $data->first_name . ' ' . $data->last_name,
                'email'      => $data->email,
                'phone'      => $data->phone,
                'dob'        => $data->dob,
                'dobb'       => date('d-m-Y', strtotime($data->dob)),
                'address'    => $data->address,
                'vision'     => $data->vision,
                'mission'    => $data->mission,
                'thumbnail'  => asset('uploads/images/'. ($data->image == "" ? 'no_image.png':"candidates/thumb/".$data->image)),
                'image'      => asset('uploads/images/'. ($data->image == "" ? 'no_image.png':"candidates/".$data->image)),
                'elections'  => $data->elections
            ];

            $elections = $data->elections;
            if (count($elections) != 0)
            {
                for ($i = 0; $i < count($elections); $i++)
                {
                    $result['elections'][$i] = [
                        'id'         => $elections[$i]->id,
                        'name'       => $elections[$i]->name,
                        'start_date' => date('d-m-Y', strtotime($elections[$i]->start_date)),
                        'end_date'   => date('d-m-Y', strtotime($elections[$i]->end_date)),
                        'thumbnail'  => asset('uploads/images/'. ($elections[$i]->image == "" ? 'no_image.png':"elections/thumb/".$elections[$i]->image)),
                        'image'      => asset('uploads/images/'. ($elections[$i]->image == "" ? 'no_image.png':"elections/".$elections[$i]->image)),
                    ];
                }
            }
            else {
                $result['elections'] = [];
            }
        }
        catch (\Exception $e) {
            return $e->getMessage();
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
        $data = [
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'dob'           => date('Y-m-d', strtotime($request->dob)),
            'address'       => strip_tags($request->address),
            'vision'        => $request->vision,
            'mission'       => $request->mission,
            'image'         => $this->upload($request->image, $this->path, $this->thumb)
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
        $candidate = $this->candidateRepository->getCandidateById($id);

        if (!$candidate)
        {
            throw new Exception("Candidate Not Found");
        }

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

        if ($request->hasFile('image'))
        {
            $data['image'] = $this->upload($request->image, $this->path, $this->thumb, $candidate->image);
        }

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
            $this->deleteImageFromDirectory($result->image, $this->path, $this->thumb);
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
