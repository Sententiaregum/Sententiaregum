#!/usr/bin/env bash

set -e

# basic database setup
mysql -e "CREATE DATABASE IF NOT EXISTS sententiaregum;"
mysql -e "CREATE DATABASE IF NOT EXISTS behat;"
echo "CREATE USER 'dev'@'localhost' IDENTIFIED BY 'dev';" | mysql -u root
echo "GRANT ALL PRIVILEGES ON * . * TO 'dev'@'localhost';" | mysql -u root
echo "FLUSH PRIVILEGES;" | mysql -u root

# install global npm dependencies
npm install -g mocha webpack eslint eslint-plugin-react less node-pre-gyp

# copy custom travis configuration
cp travis-ci/config/travis_parameters.yml app/config/parameters.yml

# composer install
composer install
