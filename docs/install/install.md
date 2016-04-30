# Install

## Manual setup

The installation is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured and the setup of the backend and frontend will be processed.

## Local nodejs environment

As a developer you may want to have a local nodejs environment. That can be helpful for local debugging as webstorm doesn't support remote node interpreters.

All linting tools must be installed globally:

The following list of npm packages must be installed globally:

- webpack
- webpack-core
- node-gyp
- mocha
- eslint
- eslint-plugin-react
- less

The dev server is currently not supported, if you'd like to auto-compile your bundle, you need to run the following command:

    npm run watch

If you'd like to make the assets production-ready, run the following:

    npm run frontend-build

A lint script has been implemented in order to execute eslint:

    npm run lint

The rest of the linters used during the build process can be found at the __before_script__ section of the [.travis.yml](https://github.com/Sententiaregum/Sententiaregum/blob/master/.travis.yml)

All local node packages and build production files will be synced.

For ESLint are some PHPStorm/WebStorm plugins available:

- [ESLint Integration for PhpStorm](https://plugins.jetbrains.com/plugin/7494)

## Deployment

### Note about fixture appliance

At the deployment for production some fixtures will be applied.
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

Capistrano is responsible for the deploy, but not for setting up the whole server. We provide a basic example script that
is runs all basic install processes: [deploy/examples/server-setup.sh](https://github.com/Sententiaregum/Sententiaregum/tree/master/deploy/examples/server-setup.sh)
