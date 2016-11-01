#!/usr/bin/env bash

set -e

# setup for node 6/7 (v6.9 is an LTS release, so we'll support it for the next time)
sudo rm -rf ~/.nvm
curl -sL "https://deb.nodesource.com/setup_${NODE_VERSION}" | sudo -E bash -
sudo apt-get install -y nodejs

# basic database setup
commands=(
  "CREATE DATABASE IF NOT EXISTS sententiaregum;"
  "CREATE DATABASE IF NOT EXISTS behat;"
  "CREATE USER 'dev'@'localhost' IDENTIFIED BY 'dev';"
  "GRANT ALL PRIVILEGES ON * . * TO 'dev'@'localhost';"
  "FLUSH PRIVILEGES;"
)
for i in "${commands[@]}"
do
  echo "${i}" | mysql -u root
done

# install global npm dependencies
sudo npm install -g mocha webpack eslint eslint-plugin-react eslint-plugin-varspacing less node-pre-gyp

# composer install
composer install
