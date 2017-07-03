<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class EmailManager
{
    protected $mailer;
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
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
        $this->mailer->send($message);
    }

    public function sendLineupReminderEmails($unsubmittedUsers,$league,$race) {
        foreach ($unsubmittedUsers as $userArr) {
            $name = $userArr['firstname'] . " " . $userArr['lastname'];
            if (trim($name) == "") { $name = $userArr['username']; }
            $this->sendEmail("Submit Lineup Reminder","cantinaband24@gmail.com","emails/lineupreminder.html.twig",['name'=>$name,'league'=>$league->getName(),'race'=>$race->getRacename()]);
        }
    }

    public function testEM() {
        return "Chad";
    }
}