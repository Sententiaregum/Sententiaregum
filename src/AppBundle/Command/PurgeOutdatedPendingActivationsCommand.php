<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> purges all pending activations that are older than two hours.

It loads all pending activations and deletes them in one big transaction.
When this command runs, it is not possible to activate a user that is scheduled for deletion inside doctrine.

It is recommended to run that as a cron job.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTimeRule = new DateTime('-2 hours');
        /** @var \AppBundle\Model\User\UserRepository $userRepository */
        $userRepository = $this->getContainer()->get('app.repository.user');

        $amount = $userRepository->deletePendingActivationsByDate($dateTimeRule);
        $output->writeln(sprintf(
            '<fg=green;bg=black>Successfully purged <comment>%d</comment> pending activations.</fg=green;bg=black>',
            $amount
        ));

        return 0;
    }
}
