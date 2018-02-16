<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LeagueController extends Controller
{

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

        // dump($totalStandings);

        $test = "leagueController";
        //dump($test);

        return $this->render(':league:standings.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'totalpoints' => $totalStandings['totalPoints'],
            'racewinners' => $totalStandings['raceWinners'],
            'fosusers' => $totalStandings['fosUsers']
        ));
    }

    /**
     * @Route("/league/members", name="app.rva.leaguemembers")
     */
    public function membersAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        $members = $LeagueManager->getLeagueMembers();

        // TODO: get only members that are active

        return $this->render(':league:members.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'data' => $members
        ));
    }

    /**
     * @Route("/league/{league_id}", name="app.rva.leaguehome", requirements={"league_id": "\d+"})
     */
    public function leaguehomeAction($league_id, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            return $this->redirectToRoute("app.rva.home");
        }

        // check if the user belongs to this league_id
        if (!$LeagueManager->isUserLeagueValid($league_id, $user)) {
            $str = "League ID is INVALID";
            dump($str);
            // redirect back to dashboard
            $leagueRenewals = [];
        } else {
            $leagueRenewals = $LeagueManager->getLeagueUpForRenew($user,$league_id);
        }

        $leagueSeasons = $LeagueManager->getAllLeagueSeasons($user);

        return $this->render(':league:home.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'league_season' => $LeagueManager->getLeagueSeason(),
            'leagueSeasons' => $leagueSeasons,
            'league_id' => $league_id,
            'leagueRenewals' => $leagueRenewals
        ));
    }

    /**
     * @Route("/league/changeseason", name="app.rva.changeseason")
     */
    public function changeLeagueSeasonAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $isAjax = $request->isXmlHttpRequest();
        $responseData = array("isAjax" => $isAjax);

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            //return $this->redirectToRoute("app.rva.home"); // TODO: need to do a jsonresponse error
            $responseData['error-pre'] = "active league or race is null - pre-season change";
        }

        $responseData['activeleague-beforechange'] = $LeagueManager->getActiveLeague();

        if ($isAjax) {
            $season = $request->request->get('season');
            $league_id = $request->request->get('lid');
            $LeagueManager->changeActiveLeague($league_id, $season);
            $responseData['season'] = $season;
            $responseData['league_id'] = $league_id;
        }


        $responseData['activeleague'] = $LeagueManager->getActiveLeague();
        $responseData['activeseason'] = $LeagueManager->getLeagueSeason();

        if (is_null($LeagueManager->getActiveLeague()) || is_null($LeagueManager->getActiveRace())) {
            $responseData['error-post'] = "active league or race is null - post-season change";
        }

        return new JsonResponse($responseData);
    }

    /**
     * @Route("/league/renew", name="app.rva.renewleague")
     */
    public function renewLeague(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $isAjax = $request->isXmlHttpRequest();
        $responseData = array("isAjax" => $isAjax);

        $LeagueManager = $this->get('app.league_manager');

        if ($isAjax) {
            $league_id = $request->request->get('lid');
            $season = date("Y");
            $LeagueManager->renewLeague($user, $league_id, $season);
            $responseData['renew'] = 'successful';
        }

        return new JsonResponse($responseData);
    }
}