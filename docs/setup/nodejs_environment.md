# Local nodejs environment

As a developer you may want to have a local nodejs environment.
This can be helpful for local debugging as webstorm doesn't support remote NodeJs interpreters.

All linting tools must be installed globally.
and tools like webpack must be present globally, too.

The following list of npm packages must be installed globally:

- webpack
- webpack-core
- node-gyp
- mocha
- eslint
- eslint-plugin-react
- eslint-plugin-varspacing
- less

The dev server is currently not supported, if you'd like to auto-compile your bundle, you need to run the following command:

    npm run watch

The build type (``production`` or ``development``) can be controlled by the ``NODE_ENV`` environment variable.
If you'd like to make the assets production-ready, run the following, you have to change the ``NODE_ENV`` variable to ``production``.

In vagrant you have to copy the following lines into the *vagrant/hieradata/local.yaml*:

``` yaml
sententiaregum::ssh::env:
  NODE_ENV: production
```

The bundle can be compiled by running the following command:

    npm run frontend

A lint script has been implemented in order to lint the JS code using ESLint and the LESS code using the LESS linter:

    npm run lint

The rest of the linters used during the build process can be found at the __before_script__ section of the [.travis.yml](https://github.com/Sententiaregum/Sententiaregum/blob/master/.travis.yml)

All local node packages and build production files will be synced.

For ESLint the [ESLint Integration for PhpStorm/WebStorm](https://plugins.jetbrains.com/plugin/7494) can be used.

## [Next (Frontend Architecture)](https://github.com/Sententiaregum/Sententiaregum/tree/master/docs/architecture/frontend.md)
