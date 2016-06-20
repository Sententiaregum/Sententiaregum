# Database Migrations

In order to change values in a DB from different versions in the 'Doctrine' way, we use [Doctrine Migrations](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html).

All migrations files are located in the directory *app/migrations* having the namespace *Sententiaregum\Migrations*.
In order to apply migrations run the following command:

``` shell
bin/console doctrine:migrations:migrate
```
