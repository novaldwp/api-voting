<?php

namespace App\Services;

use App\Interfaces\CandidateRepositoryInterface;
use App\Interfaces\ElectionRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;

class DashboardService {
    private $electionRepository;
    private $candidateRepository;
    private $userRepository;

    public function __construct(ElectionRepositoryInterface $electionRepository, CandidateRepositoryInterface $candidateRepository, UserRepositoryInterface $userRepository)
    {
        $this->candidateRepository = $candidateRepository;
        $this->electionRepository  = $electionRepository;
        $this->userRepository      = $userRepository;
    }

    public function getDataDashboard()
    {
        $role = auth()->user()->role;
        $electionOverview['label'] = [];
        $electionOverview['data'] = [];
        $electionWithTotalParticipants = $this->electionRepository->getElectionWithTotalParticipant();

        foreach ($electionWithTotalParticipants as $row) {
            array_push($electionOverview['label'], $row->name);
            array_push($electionOverview['data'], $row->votings_count);
        }

        try {
            $statusUpcoming     = "upcoming";
            $statusOnGoing      = "ongoing";
            $statusFinish       = "finish";
            $upcomingElections  = $this->electionRepository->getElections($statusUpcoming)->count();
            $ongoingElections   = $this->electionRepository->getElections($statusOnGoing)->count();
            $finishElections    = $this->electionRepository->getElections($statusFinish)->count();
            $totalElections     = $this->electionRepository->getElections()->count();
        }
        catch (\Exception $e) {
            throw new Exception("Unable to get data Elections");
        }

        $result = [
            'upcomingElections' => $upcomingElections,
            'ongoingElections'  => $ongoingElections,
            'finishElections'   => $finishElections,
            'totalElections'    => $totalElections
        ];

        if ($role == "admin") {
            try {
                $totalCandidates = count($this->candidateRepository->getCandidates());
            }
            catch (\Exception $e) {
                throw new Exception("Unable to get data Candidates");
            }

            try {
                $electionOverview['label']  = [];
                $electionOverview['labels'] = [];
                $electionOverview['data']   = [];

                $electionWithTotalParticipants = $this->electionRepository->getElectionWithTotalParticipant();

                foreach ($electionWithTotalParticipants as $row) {
                    $arr = explode(" ", $row->name);
                    if (count($arr) > 4) {
                        $arrLabel = [];
                        $chunks = array_chunk($arr, 4);
                        foreach($chunks as $chunk) {
                            array_push($arrLabel, implode(" ", $chunk));
                        }

                        array_push($electionOverview['labels'], $arrLabel);
                    }
                    else {
                        array_push($electionOverview['labels'], $row->name);
                    }
                    array_push($electionOverview['label'], $row->name);
                    array_push($electionOverview['data'], $row->votings_count);
                }
            }
            catch (\Exception $e) {
                throw new Exception("Unable to get data Election with total Participants");
            }

            try {
                $totalParticipants = $this->userRepository->getUsers(0)->count();
            }
            catch (\Exception $e) {
                throw new Exception("Unable to get data Candidates");
            }

            $result['totalCandidates']   = $totalCandidates;
            $result['totalParticipants'] = $totalParticipants;
            $result['electionOverview']  = $electionOverview;
        }

        return $result;
    }
}
