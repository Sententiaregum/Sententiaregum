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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Purger command which removes ancient auth attempt logs.
 *
 * Every attempt log item which hasn't been modified for at least ~6 months will be seen
 * as not relevant anymore and will be removed therefore.
 *
 * The attempt models are lightweight items counting failed authentications of a certain IP against
 * a user account, but if no failed authentications from an API do happen anymore or due to a false-positive
 * in the code which erases IPs from the blacklist when the login succeeds, the log is obsolete
 * and must be removed.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class PurgeAncientAuthAttemptDataCommand extends Command
{
    /**
     * @var UserWriteRepositoryInterface
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserWriteRepositoryInterface $userRepository
     */
    public function __construct(UserWriteRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sententiaregum:purge:ancient-auth-attempt-log-data')
            ->setDescription('Purges all the ancient auth attempt log items')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> purges all ancient auth attmpt log items that are older than a half year.

It searches for ancient models and deletes them in one big transaction.

It is recommended to run that as a cron job.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rule = new \DateTime('-6 months');

        $amount = $this->userRepository->deleteAncientAttemptData($rule);
        $output->writeln(sprintf(
            '<fg=green;bg=black>Successfully purged <comment>%d</comment> ancient auth models.</fg=green;bg=black>',
            $amount
        ));

        return 0;
    }
}
