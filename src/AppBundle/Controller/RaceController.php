<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceSchedule;
use AppBundle\Entity\League;
use AppBundle\Entity\RaceSubmissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Model\RaceResultStandings;

class RaceController extends Controller
{
    /**
     * @Route("/drivers", name="app.rva.selectteam")
     */
    public function driverSelectionAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $session = $request->getSession();

        if (is_null($session->get('activerace')) || is_null($session->get('activeleague'))) {
            return $this->redirectToRoute("app.rva.home");
        }

        $em = $this->getDoctrine()->getManager();
        $leagueRepo = $em->getRepository('AppBundle:League');
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $driversRepo = $em->getRepository('AppBundle:Drivers');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');

        $driversObj = $driversRepo->findBy([
            'inactive' => 0
        ]);
        $activeRace = $scheduleRepo->findOneBy([
            'activerace' => $session->get('activerace')
        ]);
        $activeLeague = $leagueRepo->findOneBy([
            'id' => $session->get('activeleague')
        ]);
        dump($activeLeague);
        dump($session->get('activeleague'));
        $raceSubmission = $raceSubmissionsRepo->findOneBy([
            'fos_user' => $user,
            'league' => $activeLeague,
            'race' => $activeRace
        ]);
dump($raceSubmission);
        $lineup_status = (isset($raceSubmission) && !empty($raceSubmission)) ? 'closed' : 'open';

        $myDrivers = [];
        if (!empty($raceSubmission)) {
            $myDrivers = $driversRepo->findAllDriversSubmitted($raceSubmission->getDrivers());
            $driver_class = "submitted";
            dump($myDrivers);
        } else {
            $driver_class = "unsubmitted";
        }

        $racelocked = false;

        return $this->render(':race:drivers.html.twig', array(
            'drivers' => $driversObj,
            'activerace' => $activeRace,
            'activeleague' => $activeLeague,
            'racesubmission' => $raceSubmission,
            'mydrivers' => $myDrivers,
            'lineup_status' => $lineup_status,
            'driver_class' => $driver_class,
            'race_locked' => $racelocked
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

        $session = $request->getSession();

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
            $raceScheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
            $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
            $leagueRepo = $em->getRepository('AppBundle:League');
            $raceSchedule = $raceScheduleRepo->findOneBy([
                'id' => $session->get('activerace')
            ]);
            $league = $leagueRepo->findOneBy([
                'id' => $session->get('activeleague')
            ]);
            $hasRaceSubmitted = $raceSubmissionsRepo->findOneBy([
                'fos_user' => $user,
                'league' => $league,
                'race' => $raceSchedule
            ]);

            if (empty($hasRaceSubmitted)) {
                $raceSubmissions->setFosUser($user);
                $raceSubmissions->setRace($raceSchedule);
                $raceSubmissions->setLeague($league);
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

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $scheduleObj = $scheduleRepo->findAll();
        $activeRace = $scheduleRepo->findOneBy(['activerace' => 1]);

        dump($scheduleObj);
        return $this->render(':race:schedule.html.twig', array(
            'schedule' => $scheduleObj,
            'activerace' => $activeRace
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

        if ($isAjax) {
            $response['available'] = true; // first set to true
            $session = $request->getSession();
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
            $raceScheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
            $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
            $leagueRepo = $em->getRepository('AppBundle:League');

            $raceSchedule = $raceScheduleRepo->findOneBy([
                'id' => $session->get('activerace')
            ]);
            $league = $leagueRepo->findOneBy([
                'id' => $session->get('activeleague')
            ]);
            $allSubmissions = $raceSubmissionsRepo->findBy([
                'race' => $raceSchedule,
                'league' => $league
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

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $leagueRepo = $em->getRepository('AppBundle:League');
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $resultsRepo = $em->getRepository('AppBundle:RaceResults');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
        $driversRepo = $em->getRepository('AppBundle:Drivers');

        $league = $leagueRepo->findOneBy(['id' => $session->get('activeleague')]);
        $raceObj = $scheduleRepo->findOneBy(['id' => $race]);
        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $raceObj, 'league' => $league ]);
        $raceResults = $resultsRepo->findOneBy(['race' => $raceObj ]);
        $league = $leagueRepo->findOneBy(['id'=>$session->get('activeleague')]);
        $drivers = $driversRepo->findAll();

        $driversArr = [];
        foreach ($drivers as $driver) {
            $driversArr[$driver->getId()] = ['name' => $driver->getFirstname() . " " . $driver->getLastname(), 'number' => $driver->getNumber()];
        }
        dump($driversArr);

        $raceResultStandings = RaceResultStandings::setResultStandings($raceResults,$raceSubmissions);
        $user_ids = array_keys($raceResultStandings['totalPoints']);
        $fosUsers = $raceSubmissionsRepo->getFosUserSubmissions($user_ids);
        $userNames = [];
        foreach ($fosUsers as $userObj) {
            $userNames[$userObj->getId()] = $userObj->getFirstname() . " " . $userObj->getLastname();
        }
        dump($userNames);
        dump($raceResultStandings);

        return $this->render(':race:results.html.twig', array(
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

        $session = $request->getSession();

        if (is_null($session->get('activeleague')) || is_null($session->get('activeleague'))) {
            return $this->redirectToRoute("app.rva.home");
        }

        $em = $this->getDoctrine()->getManager();
        $leagueRepo = $em->getRepository('AppBundle:League');
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $raceSubmissionsRepo = $em->getRepository('AppBundle:RaceSubmissions');
        $driversRepo = $em->getRepository('AppBundle:Drivers');
        $drivers = $driversRepo->findAll();
        foreach ($drivers as $driver) {
            $driversArr[$driver->getId()] = $driver;
        }

        $activeLeague = $leagueRepo->findOneBy(['id' => $session->get('activeleague')]);
        $raceObj = $scheduleRepo->findOneBy(['id' => $race]);
        $raceSubmissions = $raceSubmissionsRepo->findBy(['race' => $raceObj, 'league' => $activeLeague]);
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
dump($raceSubmissions);
dump($driversArr);
        return $this->render(':race:lineups.html.twig', array(
            'activeleague' => $activeLeague,
            'race' => $raceObj,
            'submissions' => $raceSubmissions,
            'drivers' => $driversArr,
            'users' => $fosUsers
        ));
    }

}