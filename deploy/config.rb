require 'yaml'

def parse_config(stage)
  paths = ['defaults', 'deploy', stage]
    .map { |id| "#{Dir.pwd}/deploy/config/#{id}.yaml" }
    .select { |file| File.exist?(file) }

  data = {}
  paths.each do |file|
    data.merge!(YAML::load(File.open(file)))
  end

  ['ssh_user', 'webserver_user', 'server', 'port'].each do |x|
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
      abort "Missing parameter 'domain' for production/develop server!"
    end
  end

  return data
end
