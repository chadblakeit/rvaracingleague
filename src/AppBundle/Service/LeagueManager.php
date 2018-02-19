<?php

namespace AppBundle\Service;

use AppBundle\Entity\UserLeagues;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class LeagueManager
{
    protected $activeRace;
    protected $activeLeague;
    protected $activerace_id;
    protected $activeleague_id;
    protected $league_season;
    protected $em;
    protected $session;

    public function __construct(Session $session, EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->activeleague_id = $this->session->get('activeleague'); // this is a string for some reason
        $this->activerace_id = $this->session->get('activerace');
        $this->league_season = $this->session->get('league_season');
        if (!is_null($this->activerace_id)) {
            $this->setActiveRace();
        } else {
            $this->initiateActiveRace();
        }
        if (!is_null($this->activeleague_id)) {
            $this->setActiveLeague();
        }
    }

    public function initiateActiveRace()
    {
        $raceScheduleRepo = $this->em->getRepository('AppBundle:RaceSchedule');
        $activeRace = $raceScheduleRepo->findOneBy(['activerace' => 1]);
        $this->session->set('activerace',$activeRace->getId());
        $this->activerace_id = $activeRace->getId();
        $this->activeRace = $activeRace;
    }

    public function selectLeagueFromDashboard($league_id, $fos_user)
    {
        $userLeagueRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $this->activeleague_id = $league_id;
        $leagueRepo = $this->em->getRepository('AppBundle:League');
        $this->activeLeague = $leagueRepo->findOneBy(['id' => $this->activeleague_id]);
        $this->session->set('activeleague', $this->activeleague_id);

        $userLeague = $userLeagueRepo->findOneBy(['league' => $this->activeleague_id, 'fos_user' => $fos_user],['season' => 'DESC']);
        $this->league_season = $userLeague->getSeason();
        $this->session->set('league_season', $this->league_season);
    }

    public function setActiveLeague()
    {
        $leagueRepo = $this->em->getRepository('AppBundle:League');
        $this->activeLeague = $leagueRepo->findOneBy(['id' => $this->activeleague_id]);
        //$this->setLatestLeagueSeason();
    }

    public function initiateActiveLeague($league)
    {
        $this->activeleague_id = $league->getId();
        $this->session->set('activeleague', $this->activeleague_id);
        $this->activeLeague = $league;
        //$this->setLatestLeagueSeason();
    }

    public function setLatestLeagueSeason($fos_user)
    {
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $userLeague = $userLeaguesRepo->findOneBy(['league' => $this->activeleague_id, 'fos_user' => $fos_user], ['season' => 'DESC']);
        $this->session->set('league_season', $userLeague->getSeason());
    }

    public function setLeagueSeason($season)
    {
        $this->league_season = $season;
        $this->session->set('league_season', $season);
    }

    public function isUserLeagueValid($league_id, $fos_user)
    {
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $userLeague = $userLeaguesRepo->findOneBy(['league' => $league_id, 'fos_user' => $fos_user], ['season' => 'DESC']);
        //dump($userLeague);
        if (is_null($userLeague)) {
            return false;
        } else {
            return true;
        }
    }

    public function changeActiveLeague($league_id, $season)
    {
        $leagueRepo = $this->em->getRepository('AppBundle:League');
        $activeLeague = $leagueRepo->findOneBy(['id' => $league_id]);

        $this->activeLeague = $activeLeague;
        $this->activeleague_id = $activeLeague->getId();
        $this->league_season = $season;
        $this->session->set('league_season', $season);
        $this->session->set('activeleague', $this->activeleague_id);
    }

    public function getAllLeagueSeasons($fos_user)
    {
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $userLeagues = $userLeaguesRepo->findBy(['league' => $this->activeLeague, 'fos_user' => $fos_user]);
        //dump($userLeagues);
        return $userLeagues;
    }

    public function getActiveLeague()
    {
        return $this->activeLeague;
    }

    public function setActiveRace()
    {
        $raceScheduleRepo = $this->em->getRepository('AppBundle:RaceSchedule');
        $this->activeRace = $raceScheduleRepo->findOneBy(['id' => $this->activerace_id]);
    }

    public function getActiveRace()
    {
        return $this->activeRace;
    }

    public function getLeagueSeason()
    {
        return $this->league_season;
    }

    public function getLastRaceResults()
    {
        if (is_null($this->getActiveLeague())) {
            return array('lastRace' => [], 'lastRaceWinner' => [], 'lastRacePoints' => []);
        }

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
        if (isset($userIDs[0])) {
            $userWinner = $userRepo->findOneBy(['id' => $userIDs[0]]);
            $lastRacePoints = $raceResultStandings['totalPoints'][$userIDs[0]];
        } else {
            $userWinner = [];
            $lastRacePoints = [];
        }

        //dump($userWinner);

        return array('lastRace' => $lastRace, 'lastRaceWinner' => $userWinner, 'lastRacePoints' => $lastRacePoints);
    }

    public function getTotalStandings()
    {
        $userRepo = $this->em->getRepository('AppBundle:User');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $raceResultsRepo = $this->em->getRepository('AppBundle:RaceResults');
        $raceScheduleRepo = $this->em->getRepository('AppBundle:RaceSchedule');

        // get all race_schedule races for league_season
        $raceScheduleBySeason = $raceScheduleRepo->findBy(['season' => $this->getLeagueSeason()]);
        foreach ($raceScheduleBySeason as $raceSch) {
            $scheduleIDs[] = $raceSch->getId();
        }
        //dump($scheduleIDs);
        $raceResultsByIDs = $raceResultsRepo->getResultsByRaceIDs($scheduleIDs);

        //$completedRaces = $raceResultsRepo->findAll();
        $raceIDs = array();
        $resultsArr = array();
        foreach ($raceResultsByIDs as $race) {
            $raceIDs[] = $race->getId();
            foreach ($race->getResults() as $key => $driverid) {
                $resultsArr[$race->getId()][intval($driverid)] = $key + 1;
            }
        }

        $userLeagues = $userLeaguesRepo->findBy(['league' => $this->getActiveLeague(), 'season' => $this->getLeagueSeason()]);
        foreach ($userLeagues as $userLeague) {
            $fos_user_ids[] = $userLeague->getFosUser()->getId();
        }
        $fosUsers = [];
        if (count($fos_user_ids) > 0) {
            $fos_users = $raceSubmissionsRepo->getFosUserSubmissions($fos_user_ids);
            foreach ($fos_users as $users) {
                $fosUsers[$users->getId()] = $users;
		$statsRaceWinners[$users->getId()] = 0;
            }
        }

        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $raceSubmissions = $raceSubmissionsRepo->getCompletedRaceSubmissions($raceIDs,$this->getActiveLeague());

        $totalPointsArr = [];
        $fos_user_ids = [];
        $individualRaceResults = [];

        $i=1;

        foreach ($raceSubmissions as $submission) {
            $drivers = $submission->getDrivers();
            foreach ($drivers as $driver) {
                $userPositionArr[$submission->getFosUser()->getId()][intval($driver)] = $resultsArr[$submission->getRace()->getId()][intval($driver)];
            }
            if (!isset($totalPointsArr[$submission->getFosUser()->getId()])) {
                $totalPointsArr[$submission->getFosUser()->getId()] = 0;
            }
            $totalPointsArr[$submission->getFosUser()->getId()] += array_sum($userPositionArr[$submission->getFosUser()->getId()]);
            $individualRaceResults[$submission->getRace()->getId()][$submission->getFosUser()->getId()] = array_sum($userPositionArr[$submission->getFosUser()->getId()]);
            $userPositionArr[$submission->getFosUser()->getId()] = [];
            $statsRaceWinners[$submission->getFosUser()->getId()] = 0;
            $i++;
        }

        foreach ($individualRaceResults as $rid => $raceresults) {
            asort($raceresults);
            $individualRaceResults[$rid] = $raceresults;
            $arr = reset($raceresults);
            $winner = key($raceresults);
            $statsRaceWinners[$winner] += 1;
        }
        //dump($individualRaceResults);
        //dump($statsRaceWinners);
        asort($totalPointsArr);

        return ['fosUsers' => $fosUsers, 'totalPoints' => $totalPointsArr, 'raceWinners' => $statsRaceWinners];
    }

    public function getLeagueMembers()
    {
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $fosUsers = $userLeaguesRepo->getUserInfoByLeague($this->getActiveLeague(),$this->getLeagueSeason());
        //dump($fosUsers);

        $hasSubmission = array();
        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $this->getActiveRace(), 'league' => $this->getActiveLeague()]);
        if (!empty($raceSubmissions) && !is_null($raceSubmissions)) {
            foreach ($raceSubmissions as $submission) {
                $hasSubmission[$submission->getFosUser()->getId()] = 1;
            }
        }

        //dump($hasSubmission);

        return ['members' => $fosUsers, 'submissions' => $hasSubmission];
    }

    public function getInactiveLeagues($fos_user)
    {
        $leaguesRepo = $this->em->getRepository('AppBundle:League');
        return $leaguesRepo->findBy(['disabled' => 0, 'active' => 0, 'fos_user' => $fos_user]);
    }

    public function lineupReminderForActiveLeague()
    {
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $submittedLineups = $raceSubmissionsRepo->findBy(['league'=>$this->getActiveLeague(), 'race'=>$this->getActiveRace()]);
        if (!is_null($submittedLineups) && !empty($submittedLineups)) {
            foreach ($submittedLineups as $lineup) {
                $uids[] = $lineup->getFosUser()->getId();
            }
        } else {
            $uids = [];
        }

        $unsubmittedUsers = $userLeaguesRepo->getUsersIDsNotInArray($uids,$this->getActiveLeague());
        //dump($unsubmittedUsers);
        //dump($this->getActiveLeague());
        //dump($this->getActiveRace());
        return $unsubmittedUsers;
    }

    public function getLeagueByID($lid)
    {
        $leaguesRepo = $this->em->getRepository('AppBundle:League');
        return $leaguesRepo->findOneBy(['id' => $lid]);
    }

    public function globalLineupReminder()
    {
        $raceSubmissionsRepo = $this->em->getRepository('AppBundle:RaceSubmissions');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');

        $data = $userLeaguesRepo->getDataForAllActiveLeagues();

//dump($data);

    }

    public function getLeagueUpForRenew($fos_user, $league_id)
    {
        $renewLeagueRepo = $this->em->getRepository('AppBundle:RenewLeague');
        $leagueRenewals = $renewLeagueRepo->findOneBy(['fos_user'=>$fos_user, 'league'=>$league_id, 'season'=>date("Y"), 'renewed' => 0]);
        //dump($leagueRenewals);

        return $leagueRenewals;
    }

    public function renewLeague($fos_user, $league, $season)
    {
        $renewLeagueRepo = $this->em->getRepository('AppBundle:RenewLeague');
        $userLeaguesRepo = $this->em->getRepository('AppBundle:UserLeagues');
        $leagueRepo = $this->em->getRepository('AppBundle:League');
        $userLeaguesAlreadyRenewed = $userLeaguesRepo->findOneBy(['league' => $league, 'fos_user' => $fos_user, 'season' => $season]);
        //dump($userLeaguesAlreadyRenewed);

        $leagueObj = $leagueRepo->findOneBy(['id'=>$league]);

        if (is_null($userLeaguesAlreadyRenewed)) {
            $userLeague = new UserLeagues();
            $userLeague->setSeason($season);
            $userLeague->setLeague($leagueObj);
            $userLeague->setFosUser($fos_user);
            $this->em->persist($userLeague);
            $this->em->flush();

            $renewLeague = $renewLeagueRepo->findOneBy(['league' => $league, 'fos_user' => $fos_user, 'season' => $season]);
            $renewLeague->setRenewed(1);
            $this->em->persist($renewLeague);
            $this->em->flush();

            $this->setLeagueSeason($season);
        }

    }


}