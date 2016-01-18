## Install

The installation is really simple.

You just have to call __composer install --no-dev__ and all dependencies will be configured and the setup of the backend and frontend will be processed.

#### Deployment

The deployment with capistrano is in progress in [Sententiaregum/Sententiaregum#49](https://github.com/Sententiaregum/Sententiaregum/issues/49)

At the deployment for production some fixtures will be executed.
These fixtures must implement the interface __AppBundle\Doctrine\ORM\ProductionFixtureInterface__ which extends the basic __FixtureInterface__ of the data fixtures library.

Currently the following fixtures implement this interface:

- AppBundle\DataFixtures\ORM\RoleFixture *(prior=1)*
- AppBundle\DataFixtures\ORM\AdminFixture *(prior=2)*
