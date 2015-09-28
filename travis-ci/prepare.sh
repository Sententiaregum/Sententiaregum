#!/bin/bash

mysql -e "CREATE DATABASE IF NOT EXISTS sententiaregum;"
mysql -e "CREATE DATABASE IF NOT EXISTS sententiaregum_dev;"
echo "CREATE USER 'dev'@'localhost' IDENTIFIED BY 'dev';" | mysql -u root
echo "GRANT ALL PRIVILEGES ON * . * TO 'dev'@'localhost';" | mysql -u root
echo "FLUSH PRIVILEGES;" | mysql -u root
gem install sass
npm install -g karma-cli karma-jasmine karma-phantomjs-launcher karma-browserify
composer install
php app/check.php
php app/console security:check
