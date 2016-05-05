# Vagrant

## Prerequisites

In order to run the box properly, ``vagrant`` and ``VirtualBox`` must be installed.

To run the puppet provisioners properly, the ``vagrant-r10k`` plugin and the ``puppet`` gem should be present.
This is because of the lifecycle driven by the ``r10k`` plugin.

``r10k`` deploys puppet modules declared in a ``Puppetfile`` will be deployed into another modules
 directory which is a more maintainable approach than using a hacky shell provisioner to deploy modules somewhere into the ``/etc/puppet`` directory.

## Customizing the VM

The Vagrant VM contains a sensitive configuration by default (1GB memory only) and contributors with more
powerful machines should be able to increase hardware-related settings like the memory in order to avoid issues with memory leaks
such as ``composer update`` processes in the VM.

Furthermore settings like the private network IP is modifiable to avoid collisions in the private network.
Those parameters can be customized in a file named *vagrant/machine/local.yaml*.

This may look like this:

``` yaml
# vagrant/machine/local.yaml
# NOTE: all values displayed here are the values used by default

cpus: 1
memory: 1024
name: "Sententiaregum VM"
ip: "192.168.56.232"
hostname: "sententiaregum.dev"
```

## Installation

The simplest way to install Sententiaregum is locally is using vagrant:

    git clone git@github.com:Sententiaregum/Sententiaregum.git /path/to/target
    cd /path/to/target

Now you just need to boot vagrant:

    vagrant up

Now a box with Ubuntu 14.04 (``ubuntu/trusty64``) and PHP 5.6 and NodeJS 6.x will be created automatically.

The hiera config is stored in two files managed by GIT:

- *vagrant/hieradata/common.yaml* (``common.yaml`` contains all class parameter)
- *vagrant/hieradata/classes.yaml* (``classes.yaml`` contains all classes to be managed and included by hiera)

If you'd like to override some parameters for some special cases,
you just have to create a file called *local.yaml* in the *vagrant/hieradata* directory.
This file is capable at overriding all parameters provided in the *common.yaml* file
thanks to hiera's merge feature and is ignored by GIT.
