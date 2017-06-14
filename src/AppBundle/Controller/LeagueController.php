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

        dump($totalStandings);

        $test = "leagueController";
        dump($test);

        return $this->render(':league:standings.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'totalpoints' => $totalStandings['totalPoints'],
            'racewinners' => $totalStandings['raceWinners'],
            'fosusers' => $totalStandings['fosUsers']
        ));
    }
}