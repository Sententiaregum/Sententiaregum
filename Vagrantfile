# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

Vagrant.require_version ">=1.8.4"
Vagrant.configure(2) do |config|
  # Check for required plugins
  plugin_check

  # Load settings
  settings = load_config

  # VM settings
  config.vm.box      = 'puppetlabs/ubuntu-16.04-64-puppet'
  config.vm.hostname = settings.fetch('hostname')

  config.vm.synced_folder '.', '/var/www/sententiaregum', :nfs => true
  config.vm.network :private_network, :ip => settings.fetch('ip')

  # VirtualBox provider settings
  config.vm.provider :virtualbox do |vb|
    # vb settings
    vb.name = settings.fetch('name')

    # vb customizations
    vb.customize ['modifyvm', :id, '--cpus', settings.fetch('cpus')]
    vb.customize ['modifyvm', :id, '--memory', settings.fetch('memory')]
    vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
  end

  # r10k
  config.r10k.puppet_dir      = 'vagrant/puppet'
  config.r10k.puppetfile_path = 'vagrant/puppet/Puppetfile'

  # Shell provisioner to avoid TTY issues during the puppet provisioning
  # see: https://github.com/mitchellh/vagrant/issues/1673#issuecomment-26650102
  config.vm.provision :shell do |s|
    s.privileged = false
    s.inline     = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
  end

  # Build the dev machine using puppet
  config.vm.provision :puppet do |puppet|
    puppet.environment_path  = 'vagrant/puppet/environment'
    puppet.environment       = 'sententiaregum'
    puppet.module_path       = ['vagrant/puppet/modules', 'vagrant/puppet/library']
    puppet.options           = ['--verbose --strict off']
    puppet.hiera_config_path = 'vagrant/hiera.yaml'
  end
end

def load_config
  files = ['settings', 'local']
    .map { |name| "#{Dir.pwd}/vagrant/machine/#{name}.yaml" }
    .select { |file| File.exist?(file) }

  settings = {}
  files.each do |file|
    settings.merge!(YAML::load(File.open(file)))
  end

  return settings
end

def plugin_check
  def validate(name, version = nil, optional = true)
    def get_message_type(is_optional)
      is_optional ? 'not required, but recommended' : 'required'
    end

    unless Vagrant.has_plugin?(name, version)
      type = get_message_type optional
      message = "The `#{name}` plugin is #{type}! Install it by typing `vagrant plugin install #{name}`."
      if optional
        puts message
      else
        abort message
      end
    end
  end

  validate 'vagrant-r10k', '>=0.4', false
  validate 'vagrant-cachier'
  validate 'vagrant-hostmanager'
end
