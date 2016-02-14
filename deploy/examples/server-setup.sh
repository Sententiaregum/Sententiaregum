#!/usr/bin/env bash

# basic dependencies
sudo apt-get install -y git
sudo apt-get install -y software-properties-common python-software-properties
sudo apt-get update

# php 5.6 (here with xdebug) and composer
sudo add-apt-repository ppa:ondrej/php5-5.6
sudo apt-get -y install php5
sudo apt-get -y install php5-mysql
sudo apt-get -y install php5-xdebug
sudo apt-get -y install curl

# mysql + db setup
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
sudo apt-get update
sudo apt-get install -y mysql-server
echo "CREATE DATABASE IF NOT EXISTS sententiaregum" | mysql -u root -proot
echo "CREATE USER IF NOT EXISTS 'dev'@'localhost' IDENTIFIED BY 'dev'" | mysql -u root -proot
echo "GRANT ALL PRIVILEGES ON * . * TO 'dev'@'localhost'" | mysql -u root -proot

# redis
sudo apt-get install redis-server

# nodejs
curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -
sudo apt-get install -y nodejs
