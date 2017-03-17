<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceSchedule;
use AppBundle\Entity\League;
use AppBundle\Entity\InviteUser;
use AppBundle\Entity\UserLeagues;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Controller\LeagueInviteController;
use AppBundle\Model\LeagueManager;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="app.rva.home")
     */
    public function homeAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $leagueRepo = $em->getRepository('AppBundle:UserLeagues');

        // TODO: make sure you select leagues that aren't disabled
        $myLeaguesObj = $leagueRepo->findAllMyActiveLeagues($user);

        $activeRace = $scheduleRepo->findOneBy([
            'activerace' => 1
        ]);
        $session->set('activerace',$activeRace->getId());

        $activeleague = $session->get("activeleague");
        dump($myLeaguesObj);

        if (empty($myLeaguesObj)) {
            $session->set('activeleague',null);
        }

        $activeLeagueObj = [];
        //dump($activeleague);
        // select first league by default
        if (is_null($activeleague)) {
            if (count($myLeaguesObj) >= 1) {
                $session->set('activeleague',$myLeaguesObj[0]->getLeague()->getId());
                $activeleague = $session->get("activeleague");
                $activeLeagueObj = $myLeaguesObj[0]->getLeague();
            }
        } else {
            foreach ($myLeaguesObj as $userLeague) {
                dump($userLeague);
                if ($userLeague->getLeague()->getId() == $activeleague) {
                    $activeLeagueObj = $userLeague->getLeague();
                    break;
                }
            }
        }
        //dump($activeLeagueObj);

        /* --- invited leagues --- */
        $invitedLeagues = $em->getRepository('AppBundle:InviteUser')->findAllInvitedLeagues($user->getEmail());
        //dump($invitedLeagues);
        $LeagueInvite = new LeagueInviteController();
        $LeagueInvite->inviteSalt = $this->getParameter('inviteleaguesalt');

        $LeagueManager = $this->get('app.league_manager');
        $lastRaceResults = $LeagueManager->getLastRaceResults();

        return $this->render(':league:home.html.twig', array(
            'activerace' => $activeRace,
            'lastrace' => $lastRaceResults['lastRace'],
            'lastracewinner' => $lastRaceResults['lastRaceWinner'],
            'lastracepoints' => $lastRaceResults['lastRacePoints'],
            'myleagues' => $myLeaguesObj,
            'activeleague' => $activeLeagueObj,
            'invitedleagues' => $invitedLeagues,
            'LeagueInvite' => $LeagueInvite
        ));
    }

    /**
     * @Route("/league/{league_id}", name="app.rva.selectleague", requirements={"league_id": "\d+"})
     */
    public function selectLeagueAction($league_id, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $session = $request->getSession();

        // check if the league_id and this user actually belongs to the league
        $em = $this->getDoctrine()->getManager();
        $userLeagueRepo = $em->getRepository('AppBundle:UserLeagues');
        $leagueRepo = $em->getRepository('AppBundle:League');
        $leagueObj = $leagueRepo->findOneBy(['id'=>$league_id]);
        $userLeagueObj = $userLeagueRepo->findMyActiveLeague($user,$leagueObj);

        // TODO: set error messages

        if (is_null($leagueObj)) {
            // league not found
        } else if (is_null($userLeagueObj)) {
            // user league not found
        } else {
            // everything is good, assign league id session
            $session->set('activeleague',$league_id);
        }

        return $this->redirectToRoute('app.rva.selectteam');

    }
    /**
     * @Route("/league/standings", name="app.rva.leaguestandings")
     */
    public function standingsAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $totalStandings = $LeagueManager->getTotalStandings();

        dump($totalStandings);

        return $this->render(':league:standings.html.twig', array(
            'activeleague' => $LeagueManager->getActiveLeague(),
            'totalpoints' => $totalStandings['totalPoints'],
            'fosusers' => $totalStandings['fosUsers']
        ));
    }
}