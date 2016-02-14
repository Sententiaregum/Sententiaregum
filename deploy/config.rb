require 'yaml'

def parse_config
  file_path = './deploy/config/deploy.yaml'

  unless File.exist?(file_path)
    abort "File #{file_path} not found!"
  end

  data = YAML::load(File.open(file_path))

  ['deploy_to', 'ssh_user', 'webserver_user', 'server', 'port', 'keep_releases', 'branch'].each do |x|
    if data[x].nil?
      abort "Required parameter #{x} not found in configuration hash!"
    end
  end

  if data['local_user'].nil?
    data['local_user'] = data['ssh_user']
  end

  if data['repo_url'].nil?
    data['repo_url'] = 'https://github.com/Sententiaregum/Sententiaregum.git'
  end

  ['production', 'develop'].each do |x|
    if data['server'][x].nil?
      abort "Missing environment #{x}!"
    end

    if data['server'][x]['domain'].nil?
      abort "Missing parameter 'domain' for production/dev server!"
    end
  end

  if data['allow_sudo'].nil?
    data['allow_sudo'] = false
  end

  return data
end
