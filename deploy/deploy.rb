require_relative 'config'

data = parse_config

# capistrano version
lock '3.4.0'

# basics
set :application,   'Sententiaregum'
set :repo_url,      data['repo_url']
set :deploy_to,     data['deploy_to']
set :linked_files,  fetch(:linked_files, [])
set :linked_dirs,   fetch(:linked_dirs, []).push('app/cache', 'app/logs', 'web/uploads', 'vendor')
set :keep_releases, data['keep_releases']
set :log_level,     :debug
set :scm,           :git
set :branch,        data['branch']
set :ssh_options,   {
  forward_agent: true,
}

# scm
git_username = data['git_username']
git_password = data['git_username']
unless git_username.nil? and git_password.nil?
  set :scm_username, git_username
  set :scm_password, git_password
end

# ssh
set :ssh_user,       data['ssh_user']
set :local_user,     data['local_user']
set :webserver_user, data['webserver_user']
set :ssh_port,       data['port']

# composer
set :composer_version,             '1.0.0-alpha11'
set :composer_roles,               :all
set :composer_working_dir,         -> { fetch(:release_path) }
set :composer_dump_autoload_flags, '--optimize'
set :composer_download_url,        'https://getcomposer.org/installer'

SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"

# symfony
set :symfony_console_flags, '--no-debug --no-interaction'

namespace :deploy do
  after :starting, 'composer:install_executable'

  task :migrate do
    invoke 'symfony:console', 'doctrine:migrations:migrate', '--no-interaction'
  end
end

# further tasks
namespace :npm do
  before 'composer:install_executable', :globals do
    on roles :all do
      cmd_template = 'install -g webpack webpack-core less node-pre-gyp'
      if data['allow_sudo']
        execute :sudo, "npm #{cmd_template}"
      else
        execute :npm, cmd_template
      end
    end
  end
end
