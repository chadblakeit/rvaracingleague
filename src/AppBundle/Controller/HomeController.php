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

        $LeagueManager = $this->get('app.league_manager');
        $LeagueManager->initiateActiveRace();

        $em = $this->getDoctrine()->getManager();
        $leagueRepo = $em->getRepository('AppBundle:UserLeagues');

        // TODO: make sure you select leagues that aren't disabled
        $myLeaguesObj = $leagueRepo->findAllMyActiveLeagues($user);
        dump($myLeaguesObj);

        // select first league by default
        if (is_null($LeagueManager->getActiveLeague())) {
            if (count($myLeaguesObj) >= 1) {
                $LeagueManager->initiateActiveLeague($myLeaguesObj[0]->getLeague());
            }
        }

        /* --- invited leagues --- */
        $invitedLeagues = $em->getRepository('AppBundle:InviteUser')->findAllInvitedLeagues($user->getEmail());
        $LeagueInvite = new LeagueInviteController();
        $LeagueInvite->inviteSalt = $this->getParameter('inviteleaguesalt');

        $lastRaceResults = $LeagueManager->getLastRaceResults();

        $invite = ($LeagueManager->getActiveLeague()->getFosUser()->getId() == $user->getId()) ? true : false;
dump($invite);
        return $this->render(':league:home.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'lastrace' => $lastRaceResults['lastRace'],
            'lastracewinner' => $lastRaceResults['lastRaceWinner'],
            'lastracepoints' => $lastRaceResults['lastRacePoints'],
            'myleagues' => $myLeaguesObj,
            'activeleague' => $LeagueManager->getActiveLeague(),
            'invitedleagues' => $invitedLeagues,
            'LeagueInvite' => $LeagueInvite,
            'invite' => $invite
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

        return $this->redirectToRoute('app.rva.mylineup');

    }

}