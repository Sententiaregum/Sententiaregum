load './deploy/config'

set :stage,                  :develop
set :symfony_env,            'dev'
set :branch,                 'master'
set :composer_install_flags, '--no-interaction -o'
set :controllers_to_clear,   []

data = parse_config

server data['server']['develop']['domain'], user: fetch(:ssh_user), port: fetch(:ssh_port)
