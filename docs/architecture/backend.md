# Backend Architecture

The backend uses [Symfony3](http://symfony.com/) with [Doctrine](http://www.doctrine-project.org/projects/orm.html) and further tools, based on [PHP7](https://php.net).

## Namespaces

- Everything model-related should be in `src/AppBundle/Model`. Everything in there is ordered by aggregate, your domain object
 needs to be placed in the right aggregate.
    - Handlers should be in `src/AppBundle/Model/{Aggregate}/Handler`
    - DTOs should be in `src/AppBundle/Model/{Aggregate}/DTO`
    - Command bus middlewares should be in `src/AppBundle/Model/{Aggregate}/Middleware`
    - Provider for access on certain data (the concrete implementation might be in the service namespace) the interfaces should be in `src/AppBundle/Model/{Aggregate}/Provider`
    - The domain services should be in `src/AppBundle/Model/{Aggregate}/Util`
    - Value objects for communication are in `src/AppBundle/Model/{Aggregate}/Value`
    - All repository interfaces (for model access) and models

- All tools that communicate with another service (e.g. RDBMS or [Redis](http://redis.io)) should be in `src/AppBundle/Service` where everything is ordered
 by service any subdirs.

- For listeners there's the `src/AppBundle/EventListener` namespace and for validation constraints the `src/AppBundle/Validator/Constraints`.

- Every service should be stored in a YAML file in `src/AppBundle/Resources/config`

## Business logic

The business logic should be invoked directly from an outer component such as a controller or a console command.

### Core business rules (Model)

All the core logic (behavior, basic data processing, computations, etc.) should be implemented in the model. An entity may establish
relations to internal models or to other aggregate roots (the main entity of a Model which has the same name as the model, e.g. User in `AppBundle\Model\User`).
In such structures the core of the application including its state should be implemented. For more sophisticated algorithms the code
should be factored out into a domain service which is in `AppBundle\Model\{Aggregate}\Util` and should provide its logic using the ``__invoke`` magic method.

The integration logic (communication with services and providers, data processing) should be moved into its own handler located in `AppBundle\Model\{Aggregate}\Handler`
which should use a DTO that should use public properties. For validation purposes, the customized middleware API should be used.

### Integration logic (Service)

Integration code contain processes communicating with the outer architecture or foreign services (e.g. RDBMS, Redis or a Socket).
As all of the entities are persistent in a RDBMS managed by Doctrine, the DB communicating code should implement the repository interfaces
of the corresponding aggregate (a service should __NOT__ handle processes of multiple aggregates).

Every service is located in `AppBundle\Service` is ordered by Type (e.g. Doctrine, Redis) and the structure inside this namespace can be customized for the needs.
A service communicating with services like Redis which is used in the Model (unless no such interface is required) should have two provider interfaces
in `AppBundle\Model\{Aggregate}\Provider` which is service-agnostic (means that terms like Redis shouldn't appear, but it should be named for its needs)
should be created.
For repository access some repository interfaces in `AppBundle\Model\{Aggregate}` should be created.

Every interface should be parted into two: one for read access, one for write access in order to ensure SRC and simplify the interfaces used in the core model and ease their usage.
In most of the cases one service class is enough which implements both interfaces as everythign will be resolved by assembling the service graph in the DI container.

In some cases even two classes are necessary if

- all the interaction becomes too complex
- tight coupling between read and write arises

This approach helps to simplify the usage of the third-party services however the concrete implementation doesn't matter if everything assumes to have only the interface.

### Validation

Validation is a complex part too which is coupled onto Symfony heavily. But those validators usually are quite complex and highly customized, so they should have
their own place and should be separated into its own concern, so they're located in `AppBundle\Validator\Constraints`.

### Framework usage

Sometimes some low-level symfony components such as the dispatcher or the public API of the validator are used. In this case it's
completly over-engineered to write adapters that factor out this dependency. Basically it should be ensured that __ONLY__ low-level components
which means no Kernel, DI Container or other complex or service-dependent things should be used, but those tiny interfaces can be used if necessary
for the logic.

The validator is partially too complex, so the public interface (in that case `Symfony\Component\Validator\Validator\ValidatorInterface`) should be used.
This rule with the public API of comoplex tools applies to any complex tool usage in the core model, but it needs to be evaluated whether to use this in
the model or move this part into a service.
