<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class DriversManager
{
    protected $session;
    protected $em;
    protected $driversRepo;
    protected $drivers;
    protected $driverPoints;
    protected $driverWins;
    protected $driverTopFives;
    protected $driverTopTens;
    protected $driverAvg;
    protected $driverRaces;

    public function __construct(Session $session, EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->driversRepo = $this->em->getRepository('AppBundle:Drivers');
        $this->drivers = $this->driversRepo->findAll();
        //dump($this->drivers);

    }

    public function getDriverStats() {

        $resultsRepo = $this->em->getRepository('AppBundle:RaceResults');
        $allResults = $resultsRepo->findAll();

        $driverPoints = [];
        $driverWins = [];
        $driverTopFives = [];
        $driverTopTens = [];
        $numberOfRaces = [];
        $avgFinish = [];
        $r = 0;

        foreach ($allResults as $raceResults) {

            $results = $raceResults->getResults();
            $resultsDrivers[$r] = array_values($results);

            foreach ($results as $key => $rid) {
                if (!isset($driverPoints[$rid])) { $driverPoints[$rid] = 0; $driverWins[$rid] = 0; $driverTopFives[$rid] = 0; $driverTopTens[$rid] = 0; $numberOfRaces[$rid] = 0; }
                $numberOfRaces[$rid] += 1;
                $driverPoints[$rid] += ($key+1);
                if ($key == 0) { $driverWins[$rid]++; }
                if ($key < 5) { $driverTopFives[$rid]++; }
                if ($key < 10) { $driverTopTens[$rid]++; }
            }

            $r++;
        }

        foreach ($driverPoints as $rid => $points) {
            $avgFinish[$rid] = round(($points/$numberOfRaces[$rid]),1);
            if ($numberOfRaces[$rid] != count($allResults)) {
                $d = (count($allResults) - $numberOfRaces[$rid])*50;
                $driverPoints[$rid] += $d;
            }
        }

        asort($driverPoints);
        //dump($driverPoints);

        $driversArr = [];
        $driversWithPoints = array_keys($driverPoints);
        //dump($driversWithPoints);

        foreach ($this->drivers as $driver) {
            $driversArr[$driver->getId()] = [
                'firstname' => $driver->getFirstname(),
                'lastname' => $driver->getLastname(),
                'number' => $driver->getNumber(),
                'inactive' => $driver->getInactive(),
                'carmake' => $driver->getCarmake()
            ];
            if ($driver->getInactive() == 0 && !in_array($driver->getId(),$driversWithPoints)) {
                $driverPoints[$driver->getId()] = 0;
                $driverWins[$driver->getId()] = 0;
                $driverTopFives[$driver->getId()] = 0;
                $driverTopTens[$driver->getId()] = 0;
                $avgFinish[$driver->getId()] = 0;
                $numberOfRaces[$driver->getId()] = 0;
            }
        }

        $this->driverPoints = $driverPoints;
        $this->driverWins = $driverWins;
        $this->driverTopFives = $driverTopFives;
        $this->driverTopTens = $driverTopTens;
        $this->driverAvg = $avgFinish;
        $this->driverRaces = $numberOfRaces;

        return $driversArr;
    }

    public function getDriverPoints() {
        return $this->driverPoints;
    }
    public function getDriverWins() {
        return $this->driverWins;
    }
    public function getDriverTopFives() {
        return $this->driverTopFives;
    }
    public function getDriverTopTens() {
        return $this->driverTopTens;
    }
    public function getDriverAvg() {
        return $this->driverAvg;
    }
    public function getDriverRaces() {
        return $this->driverRaces;
    }
}