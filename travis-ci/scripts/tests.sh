#!/usr/bin/env bash

set -e

echo 'PHP Test Suite'
bin/behat --strict -f progress --no-snippets --strict
bin/phpunit

# install global npm dependencies
# the javascript suite will be executed at the php 7 build
# since it isn't necessary to execute on any build the same tests with the
# same requirements.
if [ $TRAVIS_PHP_VERSION = '7' ]; then
    echo 'JavaScript Test Suite'
    npm test
fi
