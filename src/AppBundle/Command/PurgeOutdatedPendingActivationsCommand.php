<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that purges all pending activations.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PurgeOutdatedPendingActivationsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sententiaregum:purge:pending-activations')
            ->setDescription('Purger job that removes all pending activations that are outdated')
            ->setHelp(<<<EOF
The <info>%command.name%</info> purges all pending activations that are older than two hours.

It loads all pending activations and deletes them in one big transaction.
When this command runs, it is not possible to activate a user that is scheduled for deletion inside doctrine.

It is recommended to run that as a cron job.k
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTimeRule = new DateTime('-2 hours');
        /** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var \AppBundle\Model\User\UserRepository $userRepository */
        $userRepository = $doctrine->getRepository('Account:User');

        $amount = $userRepository->deletePendingActivationsByDate($dateTimeRule);
        $output->writeln(sprintf('Successfully purged <comment>%d</comment> pending activations', $amount));

        return 0;
    }
}
