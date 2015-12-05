#!/usr/bin/env bash

set -e

if [ $USER != "root" ]; then
    echo "This command requires root permissions!"
    exit 1
fi

exec_dir=$PWD
cd /etc/puppet

rm -f .vagrant-puppet-stamp
/var/www/sententiaregum/vagrant/puppet.sh

cd $exec_dir
