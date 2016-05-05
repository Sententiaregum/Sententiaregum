# Install

## Manual setup

### Prerequisites

Before running an installation, some packages must be present:

- ``mysql`` (two databases, one development and one dev database, database names can be configured in the ``parameters.yml`` during ``composer install``)
- ``PHP`` (at least ``5.6``)
- ``NodeJS`` (at least ``5.0``, ``6.0`` is recommended)
- ``Composer`` (recommended ``1.0``)
- ``Redis`` (multiple ports recommended, can be configured in the ``parameters.yml`` during ``composer install``)

### Composer install

The installation is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured
and the setup of the backend and frontend will be processed.

## Deployment

> __NOTE:__ the capistrano implementation is currently experimental and needs some more work during [#265](https://github.com/Sententiaregum/Sententiaregum/issues/265) and [#186](https://github.com/Sententiaregum/Sententiaregum/issues/186).

### Note about fixture appliance

At the deployment for production some fixtures implemented for production will be applied.
These fixtures must implement the interface __AppBundle\Doctrine\ORM\ProductionFixtureInterface__ which extends the basic __FixtureInterface__ of the data fixtures library.

Currently the following fixtures implement this interface:

- AppBundle\DataFixtures\ORM\RoleFixture *(prior=1)* (Setup for necessary roles)
- AppBundle\DataFixtures\ORM\AdminFixture *(prior=2)* (Setup to have an admin user)

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

#### Prerequirements to the server

Above some necessary packages are shown that must be present before running capistrano.
