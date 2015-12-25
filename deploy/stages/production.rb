load './deploy/config'

set :stage,                  :production
set :symfony_env,            'prod'
set :branch,                 'master'
set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader'

data = parse_config

server data['server']['production']['domain'], user: fetch(:ssh_user), port: fetch(:ssh_port)
