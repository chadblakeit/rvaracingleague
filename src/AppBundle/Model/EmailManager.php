<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\InviteUser;

class EmailManager
{
    protected $mailer;
    protected $twig;
    protected $em;
    protected $inviteUserRepo;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, EntityManager $em)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
    }

    public function sendEmail($subject,$to,$twig_template,$data) {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('chad@rvaracingleague.com')
            ->setCc('chadblakeit@gmail.com')
            ->setTo($to)
            ->setBody(
                $this->twig->render(
                    $twig_template,
                    $data
                ),
                'text/html'
            )
        ;
        return $this->mailer->send($message);
    }

    public function sendLineupReminderEmails($unsubmittedUsers,$league,$race) {
        foreach ($unsubmittedUsers as $userArr) {
            $name = $userArr['firstname'] . " " . $userArr['lastname'];
            if (trim($name) == "") { $name = $userArr['username']; }
            $this->sendEmail("Submit Lineup Reminder","cantinaband24@gmail.com","emails/lineupreminder.html.twig",['name'=>$name,'league'=>$league->getName(),'race'=>$race->getRacename()]);
        }
    }

    public function sendLeagueInviteEmail($league,$email) {

        $emailInvited = $this->inviteUserRepo->findOneBy(['league' => $league, 'email' => $email]);
        if (!empty($emailInvited) && !is_null($emailInvited)) {
            // already invited
            return false;
        }

        $inviteUser = new InviteUser();
        $inviteUser->setEmail($email);
        $inviteUser->setLeague($league);
        $inviteUser->setAccepted(0);
        $this->em->persist($inviteUser);
        $this->em->flush();

        $sent = $this->sendEmail("RVA Racing League fantasy invite from ".$league->getFosUser()->getEmail(),
            $email,
            "emails/inviteemail.html.twig",
            ['name' => 'Invited User',
             'league_name' => $league->getName(),
             'league_email' => $league->getFosUser()->getEmail(),
             'invite_email' => $email]
        );

        return $sent;
    }

    public function setInviteRepo() {
        $this->inviteUserRepo = $this->em->getRepository('AppBundle:InviteUser');
    }

    public function testEM() {
        return "Chad";
    }
}