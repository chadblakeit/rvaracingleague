<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceResults;
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AdminController extends Controller
{
    /**
     * @Route("/admin/raceresults/{race}", name="app.rva.adminraceresults")
     */
    public function raceResultsAction($race, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $role = $user->getRoles();
        dump($role);

        if (is_null($race)) {
            // redirect back to schedule
            return $this->redirectToRoute("app.rva.raceschedule");
        }

        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
        $raceResultsRepo = $em->getRepository('AppBundle:RaceResults');
        $driversRepo = $em->getRepository('AppBundle:Drivers');
        $drivers = $driversRepo->findBy([
            'inactive' => 0
        ]);
        $driverInfo = [];
        foreach ($drivers as $driver) {
            $driverInfo[$driver->getId()] = $driver;
        }

        $raceObj = $scheduleRepo->findOneBy(['id' => $race]);
        $raceResults = $raceResultsRepo->findOneBy(['race' => $raceObj]);
dump($raceResults);
dump($driverInfo);
        return $this->render(':admin:raceresults.html.twig', array(
            'race' => $raceObj,
            'role' => $role,
            'drivers' => $drivers,
            'driverInfo' => $driverInfo,
            'results' => $raceResults->getResults()
        ));
    }

    /**
     * @Route("/addsuperadminrole", name="app.rva.addsuperadminrole")
     */
    public function adminRoleAction($race, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $ids = [4];

        if (in_array($user->getId(),$ids)) {
            $em = $this->getDoctrine()->getManager();
            $user->addRole("ROLE_SUPER_ADMIN");
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute("app.rva.home");
    }

    /**
     * @Route("/admin/submitraceresults", name="app.rva.adminsubmitraceresults")
     */
    public function submitRaceResultsAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }


        if ($request->isMethod('POST')) {
            $raceResults = new RaceResults();
            $em = $this->getDoctrine()->getManager();
            $scheduleRepo = $em->getRepository('AppBundle:RaceSchedule');
            $raceResultsRepo = $em->getRepository('AppBundle:RaceResults');
            $driversRepo = $em->getRepository('AppBundle:Drivers');

            $race = $scheduleRepo->findOneBy(['id' => $request->request->get('race')]);
            $hasResults = $raceResultsRepo->findOneBy(['race' => $race]);

            if (empty($hasResults)) {
                $racePositions = $request->request->get('raceResult');

                $drivers = $driversRepo->findAll();
                $driverArr = [];
                dump($drivers);
                foreach ($drivers as $driver) {
                    $driverArr[$driver->getId()] = $driver;
                }

                /*foreach ($racePositions as $p => $rid) {
                    $raceResults->setDriver($driverArr[$rid]);
                    $raceResults->setPosition(($p+1));
                }*/

                $raceResults = new RaceResults();
                $raceResults->setRace($race);
                $raceResults->setResults($racePositions);
                $em->persist($raceResults);
                $em->flush();
            } else {
                // update race results
                $hasResults->setResults($request->request->get('raceResult'));
                $em->persist($hasResults);
                $em->flush();
            }
        }

        return new Response("<html><body>submit race results</body></html>");
    }

}