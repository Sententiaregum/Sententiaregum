#!/usr/bin/env bash

set -e

# the lint tasks will be executed at the php 5.6 build
# since it isn't necessary to execute on any build the same tests with the
# same requirements.
if [ $TRAVIS_PHP_VERSION = '5.6' ]; then
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
fi
