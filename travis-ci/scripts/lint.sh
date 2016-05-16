#!/usr/bin/env bash

set -e

# the lint tasks will be executed at the php 5.6 build
# since it isn't necessary to execute on any build the same tests with the
# same requirements.
if [ $TRAVIS_PHP_VERSION = '5.6' ]; then
    # javascript/less linting
    npm run lint

    # symfony
    php app/console security:check
    php app/console lint:yaml app/config
    php app/console lint:yaml .scrutinizer.yml
    php app/console lint:yaml .travis.yml
    php app/console lint:yaml .sensiolabs.yml
    php app/console lint:yaml behat.yml.dist
    php app/console lint:twig src
    php app/console lint:twig app
    php app/console doctrine:schema:validate
    php app/check.php
    bin/kawaii gherkin:check src/AppBundle/Features

    # PHPLoc
    ./vendor/phploc/phploc/phploc src
fi
