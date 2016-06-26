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

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Doctrine\ProductionFixtureInterface;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Command that loads custom fixtures and purges the database.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LoadCustomFixturesCommand extends DoctrineCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sententiaregum:fixtures:production')
            ->setDescription('Command that loads production data fixtures')
            ->addArgument(
                'bundles',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Alias of the bundle that production fixtures should be included',
                ['AppBundle']
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> applies all fixtures injected through the command
line as input argument.

By default you just need to enter the command without any CLI arguments:

  <info>php %command.full_name%</info>

Unless arguments were provided, 'AppBundle' will be picked as bundle.

If you'd like to load the fixtures of other bundles,
you just need to enter these bundles as CLI arguments:

  <info>php %command.full_name% CustomBundle</info>

It is also possible to chain such bundle names:

  <info>php %command.full_name% FooBundle BarBundle BazBundle</info>

All fixtures implementing the <comment>\AppBundle\Doctrine\ProductionFixtureInterface</comment>
will be loaded from the directory <comment>DataFixtures/ORM</comment> inside the bundle directory.
EOF
            );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If one of the bundles doesn't have a DataFixtures/ORM directory
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion(
                '<question>Careful, database will be purged. Do you want to continue (y/n)?</question> ',
                false
            );

            /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');
            if (!$questionHelper->ask($input, $output, $question)) {
                return 0;
            }
        }

        $bundles   = $input->getArgument('bundles');
        $container = $this->getContainer();

        /** @var \AppBundle\Doctrine\ConfigurableFixturesLoader $loader */
        $loader = $container->get('app.doctrine.fixtures_loader');
        /** @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
        $kernel = $container->get('kernel');

        $fixtures  = [];
        $instances = array_map(
            function ($bundle) use ($loader, $kernel): array {
                $absoluteBundlePath = $kernel->getBundle($bundle)->getPath();
                $fixturesPath       = sprintf('%s/DataFixtures/ORM', $absoluteBundlePath);

                if (!is_dir($fixturesPath)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Data fixtures directory "%s" does not exist!',
                        $fixturesPath
                    ));
                }

                $instanceList = $loader->loadProductionFixturesFromDirectory($fixturesPath);

                return array_map(
                    function (ProductionFixtureInterface $fixture) {
                        return get_class($fixture);
                    },
                    $instanceList
                );
            },
            $bundles
        );

        array_walk(
            $instances,
            function ($item) use (&$fixtures) {
                $fixtures = array_merge($fixtures, $item);
            }
        );

        $loader->applyFixtures($fixtures, function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        $output->writeln(PHP_EOL);
        $output->writeln('<fg=green;bg=black>Successfully applied production fixtures!</fg=green;bg=black>');

        return 0;
    }
}
