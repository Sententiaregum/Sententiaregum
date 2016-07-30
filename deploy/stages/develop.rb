require_relative '../config'

set :stage,                  :develop
set :composer_install_flags, '--no-interaction -o -q'

data = parse_config :develop

server data['server']['develop']['domain'], user: fetch(:ssh_user), port: fetch(:ssh_port)
