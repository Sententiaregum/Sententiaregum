require_relative '../config'

set :stage,                  :production
set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader -q'

data = parse_config :production

server data['server']['production']['domain'], user: fetch(:ssh_user), port: fetch(:ssh_port)
