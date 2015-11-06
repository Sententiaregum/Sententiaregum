#!/usr/bin/env bash

npm install -g node-sass --silent

# composer
# we currently need to rebuild the node-sass as the installation of that package doesn't work properly
su -l vagrant -c '(cd /var/www/sententiaregum && composer install)'

# run mailcatcher locally
lsof=`sudo lsof | grep 1025`
if [[ -z $lsof ]]; then
    sudo mailcatcher --http-ip 0.0.0.0
fi
