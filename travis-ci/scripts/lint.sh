#!/usr/bin/env bash

set -e

# the lint tasks will be executed at the php 5.6 build
# since it isn't necessary to execute on any build the same tests with the
# same requirements.
if [ $TRAVIS_PHP_VERSION = '5.6' ]; then
    # javascript/less linting
    npm run lint

    # symfony
    php bin/console security:check
    php bin/console lint:yaml app/config
    php bin/console lint:yaml .scrutinizer.yml
    php bin/console lint:yaml .travis.yml
    php bin/console lint:yaml .sensiolabs.yml
    php bin/console lint:yaml behat.yml.dist
    php bin/console lint:twig src
    php bin/console lint:twig app
    php bin/console doctrine:schema:validate
    php app/check.php
    bin/kawaii gherkin:check src/AppBundle/Features

    # PHPLoc
    ./vendor/phploc/phploc/phploc src
fi
