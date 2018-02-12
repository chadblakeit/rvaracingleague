<?php

namespace AppBundle\Service;

interface RaceResultStandingsInterface
{
    /**
     * Takes a race result and submissions and calculates the standings.
     *
     * @param array $raceResults
     * @param array $raceSubmissions
     *
     */
    public function setResultStandings($raceResults, $raceSubmissions);

    public function getUserTotalPoints();

    public function getUserDriverPositions();

    public function getDriverResults();

    public function getClass();

    public function createResultStandings();
}