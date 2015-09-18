# composer
su -l vagrant -c '(cd /var/www/sententiaregum && composer install)'

# karma
su -c '/usr/local/node/node-default/bin/npm install -g karma-phantomjs-launcher --ignore-scripts'
su -c '/usr/local/node/node-default/bin/node /usr/local/node/node-default/lib/node_modules/phantomjs/install.js'
