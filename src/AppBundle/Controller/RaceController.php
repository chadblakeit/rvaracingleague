<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceSchedule;
use AppBundle\Entity\RaceSubmissions;
use AppBundle\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use AppBundle\Service\RaceResultStandings;

class RaceController extends Controller
{

    /**
     * @Route("/lineup", name="app.rva.mylineup")
     */
    public function driverSelectionAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $DriversManager = $this->get('app.drivers_manager');
        $driversArr = $DriversManager->getDriverStats();

        $em = $this->getDoctrine()->getManager();
        $driversRepo = $em->getRepository('AppBundle:Drivers');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
        $raceSubmission = $raceSubmissionsRepo->findOneBy([
            'fos_user' => $user,
            'league' => $LeagueManager->getActiveLeague(),
            'race' => $LeagueManager->getActiveRace()
        ]);

        $lineup_status = (isset($raceSubmission) && !empty($raceSubmission)) ? 'closed' : 'open';
        $myDrivers = (!empty($raceSubmission)) ? $driversRepo->findAllDriversSubmitted($raceSubmission->getDrivers()) : [];

        // check if the race is locked
        $adminRepo = $em->getRepository('AppBundle:Admin');
        $adminResults = $adminRepo->findOneBy(['id' => 1]);
        $racelocked = ($adminResults->getLocked()) ? true : false;

        return $this->render(':race:mylineup.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'racesubmission' => $raceSubmission,
            'mydrivers' => $myDrivers,
            'lineup_status' => $lineup_status,
            'race_locked' => $racelocked,
            'driverpoints' => $DriversManager->getDriverPoints(),
            'driverwins' => $DriversManager->getDriverWins(),
            'drivertopfives' => $DriversManager->getDriverTopFives(),
            'drivertoptens' => $DriversManager->getDriverTopTens(),
            'driveravg' => $DriversManager->getDriverAvg(),
            'drivers' => $driversArr
        ));
    }

    /**
     * @Route("/race/submission", name="app.rva.racesubmission")
     */
    public function raceSubmissionAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            //return $this->redirectToRoute("app.rva.home"); // TODO: do a return jsonresponse error to redirect
        }

        $raceSubmissions = new RaceSubmissions();
        $isAjax = $request->isXmlHttpRequest();

        $array = [];
        $array["isAjax"] = $isAjax;
        $driver_ids = [];

        if ($isAjax) {

            $driver_ids = [
                $request->request->get('d1'),
                $request->request->get('d2'),
                $request->request->get('d3'),
                $request->request->get('d4'),
                $request->request->get('d5'),
                $request->request->get('d6')
            ];

            $em = $this->getDoctrine()->getManager();
            $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
            $hasRaceSubmitted = $raceSubmissionsRepo->findOneBy([
                'fos_user' => $user,
                'league' => $LeagueManager->getActiveLeague(),
                'race' => $LeagueManager->getActiveRace()
            ]);

            if (empty($hasRaceSubmitted)) {
                $raceSubmissions->setFosUser($user);
                $raceSubmissions->setRace($LeagueManager->getActiveRace());
                $raceSubmissions->setLeague($LeagueManager->getActiveLeague());
                $raceSubmissions->setDrivers($driver_ids);
                $raceSubmissions->setCreated(new \DateTime());
                $em->persist($raceSubmissions);
                $em->flush();
            } else {
                $hasRaceSubmitted->setDrivers($driver_ids);
                $em->persist($hasRaceSubmitted);
                $em->flush();
            }
        }
        $array['driver'] = $driver_ids;
        return new JsonResponse($array);
    }

    /**
     * @Route("/race/schedule", name="app.rva.raceschedule")
     */
    public function raceScheduleAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $scheduleObj = $scheduleRepo->findAll();

        //dump($scheduleObj);
        return $this->render(':race:schedule.html.twig', array(
            'schedule' => $scheduleObj,
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague()
        ));
    }

    /**
     * @Route("/race/checksubmission", name="app.rva.checkracesubmission")
     */
    public function checkRaceSubmissionAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $isAjax = $request->isXmlHttpRequest();
        $response = [];
        $response['available'] = false;

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            //return $this->redirectToRoute("app.rva.home"); // TODO: need to do a jsonresponse error
        }

        if ($isAjax) {
            $response['available'] = true; // first set to true
            $driver_ids = [
                $request->request->get('d1'),
                $request->request->get('d2'),
                $request->request->get('d3'),
                $request->request->get('d4'),
                $request->request->get('d5'),
                $request->request->get('d6')
            ];
            sort($driver_ids);

            $em = $this->getDoctrine()->getManager();
            $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
            $allSubmissions = $raceSubmissionsRepo->findBy([
                'race' => $LeagueManager->getActiveRace(),
                'league' => $LeagueManager->getActiveLeague()
            ]);

            foreach ($allSubmissions as $submission) {
                if ($submission->getFosUser()->getId() != $user->getId()) {
                    $lineup = $submission->getDrivers();
                    sort($lineup);
                    if ($driver_ids == $lineup) {
                        $response['available'] = false;
                    }
                }
            }
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/race/{race}/results", name="app.rva.raceresults")
     */
    public function raceResultsAction($race, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $resultsRepo = $em->getRepository('AppBundle:RaceResults');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
        $driversRepo = $em->getRepository('AppBundle:Drivers');

        $raceObj = $scheduleRepo->findOneBy(['id' => $race]);
        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $raceObj, 'league' => $LeagueManager->getActiveLeague() ]);
        $raceResults = $resultsRepo->findOneBy(['race' => $raceObj ]);
        $drivers = $driversRepo->findAll();

        $driversArr = [];
        foreach ($drivers as $driver) {
            $driversArr[$driver->getId()] = ['name' => $driver->getFirstname() . " " . $driver->getLastname(), 'number' => $driver->getNumber()];
        }
        //dump($driversArr);

        $raceResultStandings = RaceResultStandings::setResultStandings($raceResults,$raceSubmissions);
        $user_ids = array_keys($raceResultStandings['totalPoints']);
        $fosUsers = $raceSubmissionsRepo->getFosUserSubmissions($user_ids);
        $userNames = [];
        foreach ($fosUsers as $userObj) {
            $userNames[$userObj->getId()] = $userObj->getFirstname() . " " . $userObj->getLastname();
        }
        //dump($userNames);
        //dump($raceResultStandings);

        return $this->render(':race:results.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'race' => $raceObj,
            'driverResults' => $raceResultStandings['driverResults'],
            'userTotalPoints' => $raceResultStandings['totalPoints'],
            'userDriverPositions' => $raceResultStandings['userPositions'],
            'userNames' => $userNames,
            'drivers' => $driversArr
        ));

    }

    /**
     * @Route("/race/{race}/lineups", name="app.rva.racelineups")
     */
    public function raceLineupsAction($race, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
        $driversRepo = $em->getRepository('AppBundle:Drivers');
        $drivers = $driversRepo->findAll();
        foreach ($drivers as $driver) {
            $driversArr[$driver->getId()] = $driver;
        }

        $raceObj = $scheduleRepo->findOneBy(['id' => $race]);
        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $raceObj, 'league' => $LeagueManager->getActiveLeague()]);
        $fos_user_ids = array();
        foreach ($raceSubmissions as $key => $submission) {
            $fos_user_ids[] = $submission->getFosUser()->getId();
        }

        $fosUsers = [];
        if (count($fos_user_ids) > 0) {
            $fos_users = $raceSubmissionsRepo->getFosUserSubmissions($fos_user_ids);
            foreach ($fos_users as $users) {
                $fosUsers[$users->getId()] = $users;
            }
        }

        foreach ($raceSubmissions as $key => $submission) {
            $raceSubmissions[$key]->setFosUser($fosUsers[$submission->getFosUser()->getId()]);
        }
//dump($raceSubmissions);
//dump($driversArr);

	$show_lineup = (count($raceSubmissions) > 0) ? true : false;

        return $this->render(':race:lineups.html.twig', array(
            'activeleague' => $LeagueManager->getActiveLeague(),
            'activerace' => $LeagueManager->getActiveRace(),
            'race' => $raceObj,
            'submissions' => $raceSubmissions,
            'drivers' => $driversArr,
            'users' => $fosUsers,
	    'show_lineup' => $show_lineup
        ));
    }

    public function displayTrackLength($tracklength) {

    }

}