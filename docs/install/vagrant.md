# Vagrant

The simplest way to install Sententiaregum is locally is using vagrant:

    git clone git@github.com:Sententiaregum/Sententiaregum.git /path/to/target
    cd /path/to/target

Now you just need to boot vagrant:

    vagrant up

Now a box with Ubuntu 14.04 and PHP 5.6 will be created automatically.

The settings for the box can be found here: *vagrant/hieradata/common.yaml*

If you'd like to override some parameters for some special cases, you just need to create a file called *local.yaml* in the *vagrant/hieradata* directory.
This file is capable at overriding all parameters provided in the *common.yaml* file.

This file is optional, but will always be ignored by git.

## Windows

It is not recommended to use windows. When installing the npm dependencies on shared folders, you'll run into big trouble with the path limit.

If you use windows, [this blog post helps you to fix that issue](https://harvsworld.com/2015/how-to-fix-npm-install-errors-on-vagrant-on-windows-because-the-paths-are-too-long/).

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
