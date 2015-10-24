# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

Vagrant.configure(2) do |config|
  unless Vagrant.has_plugin?('vagrant-hostmanager')
    puts 'It is recommended to use the "vagrant-hostmanager" plugin!'
  end

  config.vm.box = "ubuntu/trusty64"
  config.vm.synced_folder ".", "/var/www/sententiaregum", :nfs => true

  config.vm.hostname = "sententiaregum"

  config.vm.provider "virtualbox" do |vb|
    # vb settings
    vb.name = "Sententiaregum VM"

    # vb customizations
    vb.customize ['modifyvm', :id, '--cpus', 1]
    vb.customize ['modifyvm', :id, '--memory', 1024]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end

  config.vm.network :private_network, :ip => '192.168.56.232'

  config.vm.provision :shell, path: "vagrant/puppet.sh"
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path    = "vagrant/manifests"
    puppet.module_path       = ['vagrant/puppet']
    puppet.manifest_file     = 'site.pp'
    puppet.options           = ["--verbose"]
    puppet.hiera_config_path = 'vagrant/hiera.yaml'
  end

  config.vm.provision :shell, path: "vagrant/post-scripts.sh"
end
