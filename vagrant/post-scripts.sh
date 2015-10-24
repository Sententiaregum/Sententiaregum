# composer
# we currently need to rebuild the node-sass as the installation of that package doesn't work properly
su -l vagrant -c '(cd /var/www/sententiaregum && npm rebuild node-sass && composer install)'
