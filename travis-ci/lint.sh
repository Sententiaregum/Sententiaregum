#!/usr/bin/env bash

set -e

# javascript/less linting
npm run lint

# symfony
bin/console security:check
bin/console lint:yaml app/config
bin/console lint:yaml .scrutinizer.yml
bin/console lint:yaml .travis.yml
bin/console lint:yaml .sensiolabs.yml
bin/console lint:yaml behat.yml.dist
bin/console lint:twig src
bin/console lint:twig app
bin/console doctrine:schema:validate
bin/symfony_requirements
bin/kawaii gherkin:check src/AppBundle/Features

# PHPLoc
./vendor/phploc/phploc/phploc src
