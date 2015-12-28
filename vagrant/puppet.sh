#!/usr/bin/env bash

if [ -f /etc/puppet/.vagrant-puppet-stamp ]; then
    exit 0
fi

# preparing
mkdir -p /etc/puppet/modules
sudo apt-get update

# install modules
puppet module install puppetlabs/stdlib --force
puppet module install puppetlabs/apt --force
puppet module install puppetlabs/mysql --force
puppet module install puppetlabs-gcc --force
puppet module install example42/puppi --force
puppet module install example42/apache --force
puppet module install example42/php --force
puppet module install willdurand/composer --force
puppet module install willdurand/nodejs --force
puppet module install thomasvandoren-redis --force
puppet module install maestrodev/wget --force
puppet module install saz-timezone --force

# create stamp file in order to mark module install completed
touch /etc/puppet/.vagrant-puppet-stamp
