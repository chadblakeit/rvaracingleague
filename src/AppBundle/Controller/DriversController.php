<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class DriversController extends Controller
{

    /**
     * @Route("/info/drivers", name="app.rva.driversinfo")
     */
    public function driverStatsAction(Request $request)
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

        $drivers = $DriversManager->getDriverStats();

        return $this->render(':info:drivers.html.twig', array(
            'activerace' => $LeagueManager->getActiveRace(),
            'activeleague' => $LeagueManager->getActiveLeague(),
            'driverpoints' => $DriversManager->getDriverPoints(),
            'driverwins' => $DriversManager->getDriverWins(),
            'drivertopfives' => $DriversManager->getDriverTopFives(),
            'drivertoptens' => $DriversManager->getDriverTopTens(),
            'driveravg' => $DriversManager->getDriverAvg(),
            'driverraces' => $DriversManager->getDriverRaces(),
            'drivers' => $drivers
        ));
    }
}