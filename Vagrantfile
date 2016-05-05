# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

Vagrant.require_version ">= 1.7"
Vagrant.configure(2) do |config|
  # Check for required plugins
  unless Vagrant.has_plugin?('vagrant-r10k', '>=0.4')
    abort 'The `r10k` plugin is required! Install by typing `vagrant plugin install vagrant-r10k`'
  end

  # Load settings
  settings = load_config

  # VM settings
  config.vm.box      = 'ubuntu/trusty64'
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

  # Provisioners
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path    = 'vagrant/manifests'
    puppet.module_path       = ['vagrant/puppet/modules', 'vagrant/puppet/library']
    puppet.manifest_file     = 'site.pp'
    puppet.options           = ['--verbose']
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
