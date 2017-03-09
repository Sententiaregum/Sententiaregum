# Install

## Manual setup

### Prerequisites

Before running an installation, some packages must be present:

- ``mysql`` (two databases, one development and one dev database, database names can be configured in the ``parameters.yml`` during ``composer install``)
- ``PHP`` (at least ``7.0``)
- ``NodeJS`` (at least ``5.0``, ``6.0`` is recommended)
- ``Composer`` (recommended ``1.0``)
- ``Redis`` (multiple ports recommended, can be configured in the ``parameters.yml`` during ``composer install``)

### Composer install

The installation is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured
and the setup of the backend and frontend will be processed.

## Deployment

### Note about fixture appliance

At the deployment for production some fixtures implemented for production will be applied.
These fixtures must implement the interface __AppBundle\Service\Doctrine\DataFixtures\ProductionFixtureInterface__ which extends the basic __FixtureInterface__ of the data fixtures library.

Currently the following fixtures implement this interface:

- AppBundle\DataFixtures\ORM\RoleFixture (Setup for necessary roles)
- AppBundle\DataFixtures\ORM\AdminFixture (Setup to have an admin user)

### Deploying with capistrano

The process can be simply triggered with ``cap deploy {stage}``. The stage can be either ``develop`` or ``production``.
As there are some sensible parameters that should not be placed in the ``deploy.rb`` itself it is possible to adjust
them through yaml:

Some basic parameters are placed in the `deploy/config/defaults.yaml`. By adding a `deploy.yaml` or `{stage}.yaml` in the same directory
those parameters can be modified.
Furthermore some mandatory parameters must be adjusted:

- `port` (SSH port where to login)
- `webserver_user`
- `local_user` (unless set, the value of `ssh_user` will be used)
- `ssh_user`

Furthermore the server must be configured properly:

``` yaml
server:
  production:
    domain: '0.0.0.0' # domain of the production server
  develop:
    domain: '0.0.0.0' # domain of the develop server
```

#### Required packages

The following gems are obligatory for deploying with capistrano:

- ``capistrano``
- ``capistrano-upload-config``
- ``capistrano-composer``

#### How to configure the application

The gem `capistrano-upload-config` helps to upload configuration files. The configuration file `app/config/parameters.yml`
is required to configure the application (e.g. redis ports and database configuration) and needs to be managed manually by uploading.
For each capistrano stage (`develop` and `production`) a custom configuration file (`parameters.{stage}.yml`) can be created containing the necessary
parameters for the stage to deploy to. The appropriate configuration file will be selected by capistrano and uploaded as `parameters.yml` and placed into
the `app/config` directory at the target server.

#### Prerequirements to the server

Above some necessary packages are shown that must be present before running capistrano.
You might also have a look at our [Testing VM](https://github.com/Sententiaregum/deployment-test-vm).
This VM is used in order to test the capistrano configuration.

## Setup with `node2nix`

This project contains a simple environment for `node2nix` to build the frontend packages.

This can be started by running `./nix-init.sh` and used by opening the shell using `nix-shell npm.nix -A shell` to package the frontend from an isolated environment.

## [Next (Custom configurations)](https://github.com/Sententiaregum/Sententiaregum/tree/master/docs/setup/custom_config.md)
