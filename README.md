# Sententiaregum
A social network based on Symfony2 and ReactJS

[![Build Status](https://travis-ci.org/Sententiaregum/Sententiaregum.svg?branch=master)](https://travis-ci.org/Sententiaregum/Sententiaregum)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Sententiaregum/Sententiaregum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Sententiaregum/Sententiaregum/?branch=master)
[![PHP 7 ready](http://php7ready.timesplinter.ch/Sententiaregum/Sententiaregum/badge.svg)](https://travis-ci.org/Sententiaregum/Sententiaregum)
[![Stories in Ready](https://badge.waffle.io/Sententiaregum/Sententiaregum.svg?label=ready&title=Ready)](http://waffle.io/Sententiaregum/Sententiaregum)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/720e0e5c-514d-4269-9d3f-c9de9ad4d7bf/mini.png)](https://insight.sensiolabs.com/projects/720e0e5c-514d-4269-9d3f-c9de9ad4d7bf)
[![Join the chat at https://gitter.im/Sententiaregum](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Sententiaregum?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![HHVM Status](http://hhvm.h4cc.de/badge/sententiaregum/sententiaregum.png)](http://hhvm.h4cc.de/package/sententiaregum/sententiaregum)

## What is it?

Sententiaregum is a social network based on a REST api written with symfony2 and a frontend in ECMAScript6 using React and Reflux.

## Vagrant

The simplest way to install Sententiaregum is locally is using vagrant:

    git clone git@github.com:Sententiaregum/Sententiaregum.git /path/to/target
    cd /path/to/target

Now you just need to boot vagrant:

    vagrant up

Now a box with Ubuntu 14.04 and PHP 5.6 will be created automatically.
There's a shell provisioner that runs the composer and npm and runs the grunt task runner that compiles the css and js.

The settings for the box can be found here: *vagrant/hieradata/common.yaml*

If you'd like to override some parameters for some special cases, you just need to create a file called *local.yaml* in the *vagrant/hieradata* directory.
This file is capable at overriding all parameters provided in the *common.yaml* file.

This file is optional, but will always be ignored by git.

#### Windows

It is not recommended to use windows. When installing the npm dependencies on shared folders, you'll run into big trouble with the path limit.

If you use windows, [this blog post helps you to fix that issue](https://harvsworld.com/2015/how-to-fix-npm-install-errors-on-vagrant-on-windows-because-the-paths-are-too-long/).

#### Local nodejs environment

As a developer you may want to have a local nodejs environment. That can be helpful for local debugging as webstorm doesn't support remote node interpreters.

You need a nodejs installation and you need to install some global packages. You just need to run the following command:

    npm install -g karma-cli webpack node-sass eslint eslint-plugin-react

There are few ruby tools that are mandatory, too:

    gem install compass
    gem install sass
    gem install scss_lint

The dev server is currently not supported, if you'd like to auto-compile your bundle, you need to run the following command:

    npm run watch

If you'd like to make the assets production-ready, run the following:

    npm run build

All local node packages and build production files will be synced.

For SCSS lint and ESLint are some PHPStorm/WebStorm plugins available:

- [SCSS Lint Integration for WebStorm](https://plugins.jetbrains.com/plugin/7530?pr=)
- [ESLint Integration for PhpStorm](https://plugins.jetbrains.com/plugin/7494)

## Deploy

A deploy is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured and the setup of the backend and frontend will be processed.

## Custom configuration

If you'd like to override configuration values, you can create for every environment in the *app/config* directory a custom configuration file:

    app/config/local_config_{environment}.yml

## License

This project is under the GPL license. The whole license file can be found [here](https://github.com/Sententiaregum/Sententiaregum/tree/master/LCIENSE)
