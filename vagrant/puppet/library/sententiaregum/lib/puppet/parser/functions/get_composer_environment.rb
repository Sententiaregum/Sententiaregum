module Puppet::Parser::Functions
  newfunction(:get_composer_environment, :type => :rvalue) do |args|
    env_data = args[0]
    if env_data.nil?
      raise Puppet::ParseError, 'get_composer_environment(): Too few arguments!'
    end

    data = ['NODE_PATH=/usr/local/node/node-default/lib/node_modules', 'HOME=/home/vagrant']
    node_env = env_data[:NODE_ENV]
    unless node_env.nil?
      data.push node_env
    end

    data
  end
end
