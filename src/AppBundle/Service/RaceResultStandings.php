<?php

namespace AppBundle\Service;

abstract class RaceResultStandings implements RaceResultStandingsInterface
{
    protected $userTotalPoints;
    protected $userDriverPositions;
    protected $driverResults;

    public function __construct()
    {

    }

    public function createResultStandings()
    {
        $class = $this->getClass();
        $results = new $class();
        return $results;
    }

    public function setResultStandings($raceResults, $raceSubmissions)
    {
        // TODO: Implement setResultStandings() method.

        //dump($raceResults);
        //dump($raceSubmissions);

        $totalPointsArr = [];
        $userPositionArr = [];
        $resultsArr = [];

        if (!is_null($raceResults)) {
            foreach ($raceResults->getResults() as $key => $driverid) {
                $resultsArr[intval($driverid)] = $key + 1;
            }

            //dump($resultsArr);

            foreach ($raceSubmissions as $submission) {
                $drivers = $submission->getDrivers();
                foreach ($drivers as $driver) {
                    $userPositionArr[$submission->getFosUser()->getId()][intval($driver)] = $resultsArr[intval($driver)];
                }
                $totalPointsArr[$submission->getFosUser()->getId()] = array_sum($userPositionArr[$submission->getFosUser()->getId()]);
                asort($userPositionArr[$submission->getFosUser()->getId()]);
            }
            asort($totalPointsArr);
        }

        //dump($totalPointsArr);
        //dump($userPositionArr);

        return ['totalPoints' => $totalPointsArr, 'userPositions' => $userPositionArr, 'driverResults' => $resultsArr];

    }

    public function getUserTotalPoints() {
        return $this->userTotalPoints;
    }

    public function getUserDriverPositions() {
        return $this->userDriverPositions;
    }

    public function getDriverResults() {
        return $this->driverResults;
    }


}