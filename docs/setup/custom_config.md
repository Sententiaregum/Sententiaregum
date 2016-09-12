# Custom configuration

If you'd like to override configuration values, you can create for every environment in the *app/config* directory a custom configuration file:

    app/config/local_config_{environment}.yml
If you want to create a new account, you will have to add a line called "secret_key" to your parameters.yml.
Then go to [this]( https://www.google.com/recaptcha/admin) page to register for a recaptcha key.
For development purposes I would use the Doctrine data fixtures.  

## [Next (Migrations)](https://github.com/Sententiaregum/Sententiaregum/tree/master/docs/setup/migrations.md)
