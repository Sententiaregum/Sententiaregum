require 'yaml'

def parse_config
  file_path = './deploy/config/deploy.yaml'

  unless File.exist?(file_path)
    abort "File #{file_path} not found!"
  end

  data = YAML::load(File.open(file_path))

  ['deploy_to', 'ssh_user', 'webserver_user', 'server', 'port', 'keep_releases'].each do |x|
    if data[x].nil?
      abort "Required parameter #{x} not found in configuration hash!"
    end
  end

  if data['local_user'].nil?
    data['local_user'] = data['ssh_user']
  end

  ['production', 'develop'].each do |x|
    if data['server'][x].nil?
      abort "Missing environment #{x}!"
    end

    if data['server'][x]['domain'].nil?
      abort "Missing parameter 'domain' for production/dev server!"
    end
  end

  return data
end
