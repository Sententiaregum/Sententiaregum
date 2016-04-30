# Vagrant

The simplest way to install Sententiaregum is locally is using vagrant:

    git clone git@github.com:Sententiaregum/Sententiaregum.git /path/to/target
    cd /path/to/target

Now you just need to boot vagrant:

    vagrant up

Now a box with Ubuntu 14.04 and PHP 5.6 will be created automatically.

The settings for the box can be found here: *vagrant/hieradata/common.yaml*

If you'd like to override some parameters for some special cases, you just need to create a file called *local.yaml* in the *vagrant/hieradata* directory.
This file is capable at overriding all parameters provided in the *common.yaml* file thanks to hiera's deep merge feature.

This file is optional, but will always be ignored by git.
