# Install

## Manual setup

The installation is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured and the setup of the backend and frontend will be processed.

## Deployment

### Note about fixture appliance

At the deployment for production some fixtures will be executed.
These fixtures must implement the interface __AppBundle\Doctrine\ORM\ProductionFixtureInterface__ which extends the basic __FixtureInterface__ of the data fixtures library.

Currently the following fixtures implement this interface:

- AppBundle\DataFixtures\ORM\RoleFixture *(prior=1)*
- AppBundle\DataFixtures\ORM\AdminFixture *(prior=2)*

### Deploying with capistrano

The process can be simply triggered with ``cap deploy {stage}``. The stage can be either ``develop`` or ``production``.
As there are some sensible parameters that should not be placed in the ``deploy.rb`` itself it is possible to adjust
them through yaml:

The ``deploy/config/deploy.yaml.dist`` is a template for such a configuration containing example parameters which are
necessary for capistrano to run. You just have to copy this file to ``deploy/config/deploy.yaml`` and then run capistrano.

#### Required packages

The following gems are obligatory for deploying with capistrano:

- ``capistrano``
- ``capistrano-symfony``
- ``capistrano-composer``

All of them are already installed in your VM.

#### Deploying with a SSH key from your VM

As deployments are usually shipped via SSH in favor of a password authentication, it should be possible to deploy your key
into the VM:

In order to deploy it to your VM just copy the file ``vagrant/ssh_key_path.yaml.dist`` to ``vagrant/ssh_key_path.yaml``.
Then uncomment the ``ssh_key`` value and adjust your the path to your own SSH key on your machine.

#### Prerequirements to the server

Capistrano is responsible for the deploy, but not for setting up the whole server. We provide a basic example script that
is runs all basic install processes: [deploy/examples/server-setup.sh](https://github.com/Sententiaregum/Sententiaregum/tree/master/deploy/examples/server-setup.sh)
