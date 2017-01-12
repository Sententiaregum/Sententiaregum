<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Model\User\UserWriteRepositoryInterface;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that purges all pending activations.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class PurgeOutdatedPendingActivationsCommand extends Command
{
    /**
     * @var UserWriteRepositoryInterface
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserWriteRepositoryInterface $repository
     */
    public function __construct(UserWriteRepositoryInterface $repository)
    {
        parent::__construct();

        $this->userRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateTimeRule = new DateTime('-2 hours');

        $amount = $this->userRepository->deletePendingActivationsByDate($dateTimeRule);
        $output->writeln(sprintf(
            '<fg=green;bg=black>Successfully purged <comment>%d</comment> pending activations.</fg=green;bg=black>',
            $amount
        ));

        return 0;
    }
}
