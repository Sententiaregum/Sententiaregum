require_relative 'config'

data = parse_config fetch :stage

# capistrano version
lock '3.4.0'

# basics
set :application,   'Sententiaregum'
set :repo_url,      data['repo_url']
set :deploy_to,     data['deploy_to']
set :linked_files,  fetch(:linked_files, ['app/config/parameters.yml'])
set :linked_dirs,   fetch(:linked_dirs, []).push('var/cache', 'var/logs', 'web/uploads', 'vendor')
set :keep_releases, data['keep_releases']
set :log_level,     :debug
set :scm,           :git
set :branch,        data['branch']
set :ssh_options,   {
  forward_agent: true,
}

# parameters
before 'deploy:check:linked_files', 'config:push'
set :config_files, ['app/config/parameters.yml']

# ssh
set :ssh_user,       data['ssh_user']
set :local_user,     data['local_user']
set :webserver_user, data['webserver_user']
set :ssh_port,       data['port']

# composer
set :composer_version,             data['composer']
set :composer_roles,               :all
set :composer_working_dir,         -> { fetch(:release_path) }
set :composer_dump_autoload_flags, '--optimize'
set :composer_download_url,        'https://getcomposer.org/installer'

# sf3 cache
namespace :cache do
  task :build do
    execute 'bin/console cache:warmup'
  end
end

after 'composer:run', 'cache:build'
