<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\UserLeagues;
use AppBundle\Entity\RenewLeague;
use AppBundle\Service\EmailManager;

class RenewLeaguesCommand extends ContainerAwareCommand
{
    protected $mailer;
    protected $twig;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:renewleagues')

            // the short description shown while running "php bin/console list"
            ->setDescription('Sends emails for renewing the leagues')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send emails for unsubmitted lineups')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $EmailManager = $this->getContainer()->get('app.email_manager');

        $em = $this->getContainer()->get("doctrine")->getManager();
        $renewLeagueRepo = $em->getRepository('AppBundle:RenewLeague');
        $userLeagueRepo = $em->getRepository('AppBundle:UserLeagues');
        $fosUserRepo = $em->getRepository('AppBundle:User');
        $leagueRepo = $em->getRepository('AppBundle:League');

        $season = date("Y",strtotime("-1 year"));

        $userLeagues = $userLeagueRepo->findBy(['season'=>$season]);

        foreach ($userLeagues as $league) {

            $hasRenewedAlready = $renewLeagueRepo->findOneBy(['fos_user' => $league->getFosUser(), 'league' => $league->getLeague(), 'season' => date("Y")]);

            if (is_null($hasRenewedAlready) || empty($hasRenewedAlready)) {
                $renewLeague = new RenewLeague();
                $renewLeague->setFosUser($league->getFosUser());
                $renewLeague->setLeague($league->getLeague());
                $renewLeague->setSeason(date("Y"));
                $renewLeague->setRenewed(0);
                $renewLeague->setDeclined(0);
                $em->persist($renewLeague);
                $em->flush();

                $fosUser = $fosUserRepo->findOneBy(['id' => $league->getFosUser()]);
                $leagueObj = $leagueRepo->findOneBy(['id' => $league->getLeague()]);
                $EmailManager->sendEmail("RVA Racing League - Renew Season", $fosUser->getEmail(), "emails/renewleague.html.twig", ['invite_email' => $fosUser->getEmail(), 'league_name' => $leagueObj->getName(), 'season' => date("Y")]);
            } else {
                dump("Already exists");
            }
        }

        $output->writeln('Renew League successfully generated!');

    }


}