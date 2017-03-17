<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Model\RaceResultStandings;

class LeagueManager
{
    protected $activeRace;
    protected $activeLeague;
    protected $activerace_id;
    protected $activeleague_id;
    protected $em;
    protected $session;

    public function __construct(Session $session, EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->activeleague_id = $this->session->get('activeleague'); // this is a string for some reason
        $this->activerace_id = $this->session->get('activerace');
        if (!is_null($this->activerace_id) || !is_null($this->activeleague_id)) {
            $this->setActiveLeague();
            $this->setActiveRace();
        }
    }

    public function setActiveLeague()
    {
        $leagueRepo = $this->em->getRepository('AppBundle:League');
        $this->activeLeague = $leagueRepo->findOneBy(['id' => $this->activeleague_id]);
        //dump($this->activeLeague);
    }

    public function getActiveLeague()
    {
        return $this->activeLeague;
    }

    public function setActiveRace()
    {
        $raceScheduleRepo = $this->em->getRepository('AppBundle:RaceSchedule');
        $this->activeRace = $raceScheduleRepo->findOneBy(['id' => $this->activerace_id]);
        //dump($this->activeRace);
    }

    public function getActiveRace()
    {
        return $this->activeRace;
    }

    public function getLastRaceResults()
    {
        $userRepo = $this->em->getRepository('AppBundle:User');
        $raceScheduleRepo = $this->em->getRepository('AppBundle:RaceSchedule');
        $raceResultsRepo = $this->em->getRepository('AppBundle:RaceResults');
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $lastRace = $raceResultsRepo->findOneBy(array(),array('id' => 'DESC'));

        $raceSchedule = $raceScheduleRepo->findOneBy(['id' => $lastRace->getId()]);
        $lastRace->setRace($raceSchedule);

        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $raceSchedule, 'league' => $this->getActiveLeague() ]);
        $raceResultStandings = RaceResultStandings::setResultStandings($lastRace,$raceSubmissions);

        $userIDs = array_keys($raceResultStandings['totalPoints']);
        $userWinner = $userRepo->findOneBy(['id' => $userIDs[0]]);
        dump($userWinner);

        return array('lastRace' => $lastRace, 'lastRaceWinner' => $userWinner, 'lastRacePoints' => $raceResultStandings['totalPoints'][$userIDs[0]]);
    }

    public function getTotalStandings()
    {
        $userRepo = $this->em->getRepository('AppBundle:User');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $raceResultsRepo = $this->em->getRepository('AppBundle:RaceResults');
        $completedRaces = $raceResultsRepo->findAll();
        $raceIDs = array();
        foreach ($completedRaces as $race) {
            $raceIDs[] = $race->getId();
            foreach ($race->getResults() as $key => $driverid) {
                $resultsArr[$race->getId()][intval($driverid)] = $key + 1;
            }
        }

        $userLeagues = $userLeaguesRepo->findBy(['league' => $this->getActiveLeague()]);
        foreach ($userLeagues as $userLeague) {
            $fos_user_ids[] = $userLeague->getFosUser()->getId();
        }
        $fosUsers = [];
        if (count($fos_user_ids) > 0) {
            $fos_users = $raceSubmissionsRepo->getFosUserSubmissions($fos_user_ids);
            foreach ($fos_users as $users) {
                $fosUsers[$users->getId()] = $users;
            }
        }

        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $raceSubmissions = $raceSubmissionsRepo->getCompletedRaceSubmissions($raceIDs,$this->getActiveLeague());

        $totalPointsArr = [];
        $fos_user_ids = [];

        foreach ($raceSubmissions as $submission) {
            $drivers = $submission->getDrivers();
            foreach ($drivers as $driver) {
                $userPositionArr[$submission->getFosUser()->getId()][intval($driver)] = $resultsArr[$submission->getRace()->getId()][intval($driver)];
            }
            if (!isset($totalPointsArr[$submission->getFosUser()->getId()])) {
                $totalPointsArr[$submission->getFosUser()->getId()] = 0;
            }
            $totalPointsArr[$submission->getFosUser()->getId()] += array_sum($userPositionArr[$submission->getFosUser()->getId()]);
            $userPositionArr[$submission->getFosUser()->getId()] = [];
        }

        asort($totalPointsArr);

        return ['fosUsers' => $fosUsers, 'totalPoints' => $totalPointsArr];
    }

}