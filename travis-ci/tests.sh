#!/usr/bin/env bash

set -e

echo 'PHP Test Suite'
bin/behat --strict -f progress --no-snippets --strict
bin/phpunit

echo 'JavaScript Test Suite'
npm test
