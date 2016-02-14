require_relative '../config'

set :stage,                  :production
set :symfony_env,            'prod'
set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'

data = parse_config

server data['server']['production']['domain'], user: fetch(:ssh_user), port: fetch(:ssh_port)
