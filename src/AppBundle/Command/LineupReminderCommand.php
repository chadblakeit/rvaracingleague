<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class LineupReminderCommand extends ContainerAwareCommand
{
    protected $mailer;
    protected $twig;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:lineupreminder')

            // the short description shown while running "php bin/console list"
            ->setDescription('Sends email reminders for unsubmitted lineups')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send reminder emails for unsubmitted lineups')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $EmailManager = $this->getContainer()->get('app.email_manager');

        $output->writeln('User successfully generated!'.$EmailManager->testEM());

    }


}