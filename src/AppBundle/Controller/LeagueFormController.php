<?php

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\InviteUser;
use AppBundle\Entity\User;
use AppBundle\Entity\UserLeagues;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Controller\LeagueInviteController;

class LeagueFormController extends Controller
{

    /**
     * @Route("/league/create", name="app.rva.leaguecreate")
     */
    public function leagueAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }


        // TODO: prevent double submission - reloading a submission currently adds a new league with the same name


        $league = new League();
        $form = $this->createFormBuilder($league)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create League'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $league = $form->getData();
            $league->setFosUser($user);
            $league->setActive(0);
            $league->setDisabled(0);
            $league->setConfirmation("");
            $league->setCreated = new \DateTime("now");
            //$team->setExpire = new \DateTime("+2 days");

            $em = $this->getDoctrine()->getManager();
            $em->persist($league);
            $em->flush();

            $salt = md5($this->getParameter('activatesalt') . $user->getId() . $league->getName() . $league->getId());
            $url = "http://rva.dev/league/activate?u=".base64_encode($user->getId())."&ac=".$salt."&l=".$league->getId();

            $message = \Swift_Message::newInstance()
                ->setSubject('Activation Link for rvaracingleague.com')
                ->setFrom('web@rvaracingleague.com/')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/activateemail.html.twig',
                        array('url' => $url, 'leaguename' => $league->getName())
                    ),
                    'text/html'
                );
            $test = $this->get('mailer')->send($message);

            return $this->render(':league:league.html.twig', array(
                'register' => true,
                'email' => $user->getEmail(),
                'email_sent' => $test
            ));
        }

        return $this->render(':league:league.html.twig', array(
            'form' => $form->createView(),
            'email' => $user->getEmail()
        ));
    }

    /**
     * @Route("/league/activate", name="app.rva.leagueactivate")
     */
    public function activateLeagueAction(Request $request)
    {
        $activate_code = $request->query->get('ac');
        $codeduser = $request->query->get('u');
        $league_id = $request->query->get('l');
        $decodeduser = base64_decode($codeduser);

        if (!is_null($activate_code) && !empty($activate_code)) {

            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository('AppBundle:User');
            $userLeagueRepo = $em->getRepository('AppBundle:UserLeagues');
            $leagueRepo = $em->getRepository('AppBundle:League');
            $leagueUser = $userRepo->findOneBy(['id'=>$decodeduser]);
            $leagueObj = $leagueRepo->findOneBy([
                'fos_user' => $leagueUser,
                'active' => 0,
                'id' => $league_id
            ]);

            if (!empty($leagueObj) && !is_null($leagueObj)) {

                $key = $this->getParameter('activatesalt');
                $decode = md5($key . $decodeduser . $leagueObj->getName() . $leagueObj->getId());
                $activated = false;

                if ($decode === $activate_code) {
                    // activate account
                    $leagueObj->setActive(1);
                    $em->persist($leagueObj);
                    $em->flush();

                    // set league creator as a member of their own league
                    $userLeague = new UserLeagues();
                    $userLeague->setFosUser($leagueUser);
                    $userLeague->setLeague($leagueObj);
                    $em->persist($userLeague);
                    $em->flush();
                } else {

                }

                $session = $request->getSession();
                $session->set('activeleague',$leagueObj->getId());

                return $this->redirectToRoute("app.rva.leagueinvite");

            } else {
                // league already active , TODO: show error msg


                // TODO: add flash message

                return $this->redirectToRoute("app.rva.home");
            }

        } else {
            return new Response("<html><body>Activation Code not available!!</body></html>");
        }

    }

    /**
     * @Route("/league/invite", name="app.rva.leagueinvite")
     */
    public function inviteTeamMember(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        if (is_null($LeagueManager->getActiveLeague())) {
            // redirect to home to select a league
            // TODO: show flash message
            return $this->redirectToRoute("app.rva.home");
        }

        $EmailManager = $this->get('app.email_manager');
        $EmailManager->setInviteRepo();

        $user = array();
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Invite'))
            ->getForm();

        $form->handleRequest($request);
        $invited = [];
        $already_invited = [];

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            if (strstr($data['email'],",")) {
                $emails = explode(",",$data['email']);
                dump($emails);
                for ($e=0; $e<count($emails); $e++) {
                    if (!empty(trim($emails[$e]))) {
                        // TODO: check for valid email
                        $sent = $EmailManager->sendLeagueInviteEmail($LeagueManager->getActiveLeague(),trim($emails[$e]));
                        if ($sent) {
                            $invited[] = trim($emails[$e]);
                        } else {
                            $already_invited[] = trim($emails[$e]);
                        }
                    }
                }
            } else {
                $sent = $EmailManager->sendLeagueInviteEmail($LeagueManager->getActiveLeague(),trim($data['email']));
                if ($sent) {
                    $invited[] = trim($data['email']);
                } else {
                    $already_invited[] = trim($data['email']);
                }
            }
        }

        return $this->render(':league:invite.html.twig', array(
            'form' => $form->createView(),
            'raceleagueid' => $LeagueManager->getActiveLeague()->getId(),
            'invited' => $invited,
            'already_invited' => $already_invited,
            'league' => $LeagueManager->getActiveLeague()
        ));
    }

    /**
     * @Route("/league/join", name="app.rva.joinleague")
     */
    public function joinLeague(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $userTeams = array();
        $em = $this->getDoctrine()->getManager();
        $invitedLeagues = $em->getRepository('AppBundle:InviteUser')
            ->findAllInvitedLeagues($user->getEmail());

        $LeagueInvite = new LeagueInviteController();
        $LeagueInvite->inviteSalt = $this->getParameter('inviteleaguesalt');

        return $this->render(':league:join.html.twig', array(
            'user' => $user,
            'invitedLeagues' => $invitedLeagues,
            'LeagueInvite' => $LeagueInvite
        ));
    }

    /**
     * @Route("/league/acceptinvite", name="app.rva.leagueacceptinvite")
     */
    public function acceptInvite(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $userLeagues = new UserLeagues();
        $isAjax = $request->isXmlHttpRequest();

        $array = [];
        $array["isAjax"] = $isAjax;

        if ($isAjax) {

            $inviteleague_email = $request->request->get('inviteleague_email');
            $inviteleague = $request->request->get('inviteleague');
            $leagueindex = $request->request->get('leagueindex');
            $salt = $this->getParameter('inviteleaguesalt');

            $em = $this->getDoctrine()->getManager();
            $leagueRepo = $em->getRepository('AppBundle:League');
            $inviteRepo = $em->getRepository('AppBundle:InviteUser');
            $userLeagueRepo = $em->getRepository('AppBundle:UserLeagues');
            $userRepo = $em->getRepository('AppBundle:User');
            $userObj = $userRepo->findOneBy(['email'=>$inviteleague_email]);

            $leaguesObj = $leagueRepo->findAll([
                'fos_user' => $userObj
            ]);

            $array['leagueindex'] = $leagueindex;
            $array['invitesuccess'] = false;
            $array['hasUserLeague'] = false;

            foreach ($leaguesObj as $league) {
                if (md5($league->getId().$inviteleague_email.$salt) == $inviteleague) {
                    $hasUserLeague = $userLeagueRepo->findOneBy([
                        'league' => $league,
                        'fos_user' => $user
                    ]);
                    if (empty($hasUserLeague)) {
                        $userLeagues->setFosUser($user);
                        $userLeagues->setLeague($league);
                        $em->persist($userLeagues);
                        $em->flush();
                        $array['invitesuccess'] = true;

                        $inviteUser = $inviteRepo->findOneBy([
                            'email' => $user->getEmail(),
                            'league' => $league
                        ]);
                        $inviteUser->setAccepted(1);
                        $em->persist($inviteUser);
                        $em->flush();
                    } else {
                        $array['hasUserLeague'] = true;
                    }
                    break;
                }
            }
        }


        return new JsonResponse($array);
    }

    /**
     * @Route("/league/resendactivation", name="app.rva.resendactivation")
     */
    public function resendActivationAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $LeagueManager = $this->get('app.league_manager');
        $EmailManager = $this->get('app.email_manager');

        $isAjax = $request->isXmlHttpRequest();

        $array = [];
        $array["isAjax"] = $isAjax;

        if ($isAjax) {
            $lid = $request->request->get('l');
            $league = $LeagueManager->getLeagueByID($lid);
            $salt = md5($this->getParameter('activatesalt') . $user->getId() . $league->getName() . $league->getId());
            $array['sent'] = $EmailManager->resendLeagueActivation($salt,$league,$user);
        }

        return new JsonResponse($array);
    }

}