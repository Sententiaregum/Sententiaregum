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

declare (strict_types = 1);

namespace AppBundle\Command;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * InstallDatabaseCommand.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class InstallDatabaseCommand extends ContainerAwareCommand
{
    const STRATEGY_SCHEMA_UPDATE = 'schema-update';
    const STRATEGY_MIGRATIONS    = 'migrations';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sententiaregum:install:database')
            ->setDescription('Command that installs the database for a certain environment')
            ->addArgument(
                'managers',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of managers to be validated',
                ['default']
            )
            ->addOption(
                'apply-fixtures',
                null,
                InputOption::VALUE_NONE,
                'If set, fixtures will be applied if the whole database is empty'
            )
            ->addOption(
                'strategy',
                's',
                InputOption::VALUE_OPTIONAL,
                'Sets the strategy how to install the database (either `schema-update` or `migrations)',
                self::STRATEGY_SCHEMA_UPDATE
            )
            ->addOption(
                'production-fixtures',
                null,
                InputOption::VALUE_NONE,
                'If set, only production fixtures will be applied'
            )
            ->addOption(
                'append',
                'a',
                InputOption::VALUE_NONE,
                'If set, the fixtures will be appended'
            )
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> uses the SchemaTool of the ORM to ensure that the database is installed.
Unless, a install process will be fired.

Calling the command for the prod environment:
<info>bin/console sententiaregum:install:database --env=prod</info>

If the database is empty, it's sometimes helpful to apply fixtures.
If the database isn't empty, in the interactive mode it will be asked whether to apply fixtures
anyway, if this mode is disabled too, then the fixtures option will be ignored.

This can be achieved by using the `--apply-fixtures` option:

<info>bin/console sententiaregum:install:database --apply-fixtures</info>

If the prod environment is set, the `LoadCustomFixturesCommand` will be used.

To use doctrine migrations for schema updates, the `--strategy` option must be set to `migrations`:

<info>bin/console sententiaregum:install:database --strategy=migrations</info>

To apply production fixtures only, use the `--production-fixtures` option:

<info>bin/console sententiaregum:install:database --apply-fixtures --production-fixtures</info>

To append fixtures, use the `--append` option.
NOTE: this feature is not available with `--production-fixtures`.

<info>bin/console sententiaregum:install:database --apply-fixtures --append</info>
EOF
);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If the strategy is invalid.
     * @throws \InvalidArgumentException If the fixtures config is invalid.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $environment         = $this->getContainer()->getParameter('kernel.environment');
        $application         = $this->getApplication();
        $strategy            = $input->getOption('strategy');
        $productionFixtures  = $input->getOption('production-fixtures');
        $validatedDatabases  = [];

        if (!in_array($strategy, [self::STRATEGY_SCHEMA_UPDATE, self::STRATEGY_MIGRATIONS], true)) {
            throw new \InvalidArgumentException(sprintf(
                'The strategy must be either "%s" or "%s"!',
                self::STRATEGY_MIGRATIONS,
                self::STRATEGY_SCHEMA_UPDATE
            ));
        }

        /** @var \Doctrine\Common\Persistence\ManagerRegistry $registry */
        $registry = $this->getContainer()->get('doctrine');

        // initialize counters
        $count          =
        $applied        =
        $fixtureApplied =
        $inSync         = 0;

        foreach ($input->getArgument('managers') as $manager) {
            $output->writeln(sprintf('<comment>Applying schema for manager "%s"...</comment>', $manager));
            ++$count;

            /** @var \Doctrine\ORM\EntityManagerInterface $em */
            $em        = $registry->getManager($manager);
            $tool      = new SchemaTool($em);
            $validator = new SchemaValidator($em);

            // the SchemaValidator is able to test the internal entity metadata
            // against the database DDL. If multiple entity managers have the same connection,
            // every validator instance will ONLY check the metadata of its associated
            // entity manager.
            if (!$validator->schemaInSyncWithMetadata()) {
                // execute strategy by schema
                if (self::STRATEGY_SCHEMA_UPDATE === $strategy) {
                    // At this place we UPDATE the whole schema instead of applying everything:
                    // This is due to the issue that a schema can be partially in sync, but this tool would detect
                    // the schema of the corresponding entity manager as outdated and would run the appliance again.
                    // If the appliance would force to recreate everything, tons of `Table or view exists` errors
                    // would arise.
                    $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
                } else {
                    // Especially on prod environments it's safer to rely on the migrations framework.
                    $command        = $application->find('doctrine:migrations:migrate');
                    $migrationInput = new ArrayInput([
                        '--env' => $environment,
                    ]);

                    // migrations should be used in order to ensure on production environments
                    // with already existing data that the data will be preserved
                    // using custom migration classes for DDL changes.
                    $migrationInput->setInteractive($input->isInteractive());
                    $command->run($migrationInput, new NullOutput());
                }

                ++$applied;
            } else {
                ++$inSync;
            }

            $shouldApplyFixtures = $input->getOption('apply-fixtures');
            if ($productionFixtures && !$shouldApplyFixtures) {
                throw new \InvalidArgumentException(
                    'The `--production-fixtures` option must not be set if the `--apply-fixtures` option is not present!'
                );
            }

            // fixture appliance:
            // the database installation process may include a fixture appliance, if the `--apply-fixtures` flag
            // is set.
            // If configured, only production fixtures will be applied.
            if ($shouldApplyFixtures) {
                $connection   = $em->getConnection();
                $database     = $connection->getDatabase();
                $shouldAppend = $apply = $input->getOption('append');

                // ensure that the validation is not executed multiple times.
                // if two entity managers have the same connection, but different metadata
                // and the database is not empty, this information should be cached in order
                // to avoid calculations when possible.
                if (!isset($validatedDatabases[$database]) && !$shouldAppend) {
                    // build an SQL query which ensures that the database is empty
                    $migrationsTable = $this->getContainer()->getParameter('doctrine_migrations.table_name');
                    $template        = '(SELECT COUNT(:col) FROM :table)';
                    $tables          = $connection->getSchemaManager()->createSchema()->getTables();
                    $tableCountSQL   = array_map(function (Table $table) use ($template, $migrationsTable): string {
                        $name = $table->getName();

                        // migration table needs to be skipped.
                        if ($migrationsTable === $name) {
                            return '(0)';
                        }

                        $primaryKeys = $table->getPrimaryKeyColumns();
                        $column      = count($primaryKeys) === 0 ? '*' : $primaryKeys[0];

                        return strtr($template, [
                            ':col'   => $column,
                            ':table' => $name,
                        ]);
                    }, $tables);

                    $statement = $connection->prepare(sprintf('SELECT (%s) AS rows', implode('+', $tableCountSQL)));
                    $statement->execute();

                    // if the result is higher than zero, the appliance should be skipped
                    // since it the table is not empty.
                    $validatedDatabases[$database] = $apply = (int) $statement->fetch()['rows'] === 0;
                } elseif (isset($validatedDatabases[$database])) {
                    $apply = $validatedDatabases[$database];
                }

                if ($input->isInteractive() && !$apply) {
                    $question = new ConfirmationQuestion(
                        '<question>Careful, database is not empty. Should the fixtures be applied anyway (y/n)?</question>',
                        false
                    );

                    /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
                    $questionHelper = $this->getHelper('question');
                    if (!$questionHelper->ask($input, $output, $question)) {
                        $output->writeln(sprintf(
                            '<comment>Skipping fixture appliance for manager "%s"!</comment>',
                            $manager
                        ));

                        continue;
                    }

                    $apply = true;
                }

                // checks whether fixtures should be applied (`--apply-fixtures`) and whether the database
                // is empty. Unless the whole process will be interrupted to avoid clearing accidentally the database.
                if ($apply) {
                    $target = $productionFixtures
                        ? 'sententiaregum:fixtures:production'
                        : 'doctrine:fixtures:load';

                    $output->writeln(sprintf(
                        '<comment>Applying fixtures for manager "%s" with environment "%s"!</comment>',
                        $manager,
                        $environment
                    ));

                    $args = ['--env' => $environment];
                    if ($shouldAppend) {
                        // the `LoadCustomFixturesCommand` doesn't support appending due to the fact that
                        // the production fixtures are only some very basic items that don't need to be appended,
                        // but should be executed at first and changed by migrations (model partially depends).
                        if ($productionFixtures) {
                            $output->writeln(sprintf(
                                '<error>Skipping `--append` flag for manager "%s" because `--production-fixtures` option is set</error>',
                                $manager
                            ));

                            continue;
                        } else {
                            $args['--append'] = true;
                        }
                    }

                    $command      = $application->find($target);
                    $fixtureInput = new ArrayInput($args);

                    $fixtureInput->setInteractive($input->isInteractive());
                    $command->run($fixtureInput, new NullOutput());

                    ++$fixtureApplied;

                    continue;
                }
            }

            // the skip notice should be displayed all the time if the fixture flag is set,
            // but the notice will be skipped.
            $message = sprintf(
                'Skipping fixture appliance for manager "%s" because the database is not empty %s',
                $manager,
                $input->isInteractive()
                    ? ' and the confirmation has been answered with no'
                    : ' and the interaction mode is disabled, so no confirmation whether to force appliance can be used'
            );

            $output->writeln(sprintf(
                '<comment>%s!</comment>',
                $message
            ));
        }

        // add an empty line before result stats
        $output->writeln('');
        $output->writeln(sprintf(
            '<info>Validated %d %s, %d needed schema appliance, %d %s in sync. %d %s %s fixtures were applied.</info>',
            $count,
            $this->pluralize($count, 'managers', 'manager'),
            $applied,
            $inSync,
            $this->pluralize($count, 'were', 'was'),
            $fixtureApplied,
            $this->pluralize($fixtureApplied, 'times', 'time'),
            $productionFixtures ? 'production' : 'all'
        ));

        return 0;
    }

    /**
     * Simple shortcut to pluralize expressions in error messages.
     *
     * @param int    $count
     * @param string $plural
     * @param string $singular
     *
     * @return string
     */
    private function pluralize(int $count, string $plural, string $singular): string
    {
        return $count === 1 ? $singular : $plural;
    }
}
