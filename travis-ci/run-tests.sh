#!/bin/bash

# backend
bin/behat --strict -f progress --no-snippets
bin/phpunit -c app

# frontend
karma start --single-run
