# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  # Check for require plugins
  unless Vagrant.has_plugin?('vagrant-r10k')
    abort 'The `r10k` plugin is required! Install by typing `vagrant plugin install vagrant-r10k`'
  end

  # VM settings
  config.vm.box = 'ubuntu/trusty64'
  config.vm.hostname = "sententiaregum.dev"
  config.vm.synced_folder '.', '/var/www/sententiaregum', :nfs => true
  config.vm.network :private_network, :ip => '192.168.56.232'

  # VirtualBox provider settings
  config.vm.provider :virtualbox do |vb|
    # vb settings
    vb.name = "Sententiaregum VM"

    # vb customizations
    vb.customize ['modifyvm', :id, '--cpus', 1]
    vb.customize ['modifyvm', :id, '--memory', 1024]
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
