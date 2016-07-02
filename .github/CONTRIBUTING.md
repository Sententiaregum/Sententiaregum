Contributing Guide Line for Sententiaregum
==========================================

This guideline shows some conventions when contributing to Sententiaregum, so your Pull Request can be handled faster.

1) Code style
-------------

Your patch __must__ follow the [symfony coding standards](http://symfony.com/doc/current/contributing/code/standards.html)

For the javascript frontend we use the tools scss-lint and eslint that will be installed automatically on the VM.
If your code violates one of their convetions, the travis build will break and github blocks any attempt to merge your PR.

__Note:__ before sending a PR that changes PHP code, please run the following command:

    bin/php-cs-fixer fix

So you don't need to worry about code style. If you don't do and there are still code style issues, then the PR cannot be merged since StyleCI will fail.
The JavaScript CS will be validated using eslint.

To view javascript errors (cannot be fixed automatically atm), run the following command:

    npm run lint

2) Branch name, user stories and commit message
-----------------------------------------------

### Branch name

If you'd like to fix an issue, the branch name should look like this: 

    #{issue key}-short-description

If you'd like to add a new feature without an issue, the branch should contain the name of the feature separated with dashes.
So if you add a realtime messenger to this application, the branch could look like this:

    short-description

### Stories

####When assigning yourself to a ticket

- Stories **MUST** not have an assignee. This is due to the fact, that it is quite impossible to work on big ones by yourself.
- In case of a story the commits should **NOT** be squashed, as a story contains a lot of sub-tasks (which on the other hand should be squashed) written in the PR of the story. 
- Every story has to have at least **two** reviewers. 

### Commit message

#### When fixing a ticket

When fixing an issue, the issue should have a red label (e.g. Infrastructure/Maintenance/Bug).
Then the commit message should look like this:

    {Issue type (e.g. Infrastructure/Maintenance/Bug} #{ticket number} {short description what you've done}

If there's no red label at the ticket, the issue type is __Minor__.

#### When adding a new feature

As there are no labels, the commit message should look like this:

    [Feature] {short description what you've done}

The keyword in the square brackets can be someone of those:

- **Bug**
- **Code Quality**
- **Minor**
- **Feature**
- **Documentation**
- **Enhancement**
- **Improvement**
- **Refactor**
- **Security**
- **Support/Maintenance**

If there's a label added to your PR, the commit message should be renamed to the following pattern:

    {Issue Type (red label)} {short description what you've done}


3) Pull Request
---------------

The pull request should have some content, too.
You just need to tell what has changed.

Please add: 
    ```Resolves #{issue}``` in every pull request.

This is needed, so that waffle.io can attach the PR to the issue card.

__Note__: if you have fixed a bug that is not reported in the issue tracker, please add a detailed description how this bug was caused.

#### Reviews

If a line note is added, you should fix that. If there's a reason why that should not be fixed, please comment on the diff, too.
If the review is ready and at least one collaborator or owner gave thumbs up, you should squash all of your commit into a big one.

4) Tests
--------

Every change requires a testcase. The following sections will explain how to implement those:

### 4.1) Backend

The backend contains two test suites: the unit tests based on PHPUnit and functional tests based on Behat.

When changing behavior in a low level component such as the business logic, a validator or doctrine extensions (e.g. DBAL types or DQL functions)
the change requires a test case in the PHPUnit testsuite.
Furthermore commands should also have a UnitTest since commands are wrappers to give the ability of executing certain things from the CLI
such as administrative action (e.g. apply fixture data for production, run migration actions) or jobs for a queue or crontab (e.g. the purge jobs),
so the test should cover the I/O behavior of the command the way how the command communicates with the business logic.
More complex commands (e.g. the fixture appliance workflow) should have a functional test in order to verify the whole behavior properly.

Infrastructural services (e.g. doctrine repositories or wrappers for redis functionality) should have a functional test since
the inclusion of external services (such as a database) needs its own test case and especially the behavior of doctrine needs integrative
tests.

The REST API represents the whole features of this application being used by the frontend. In order to ensure the appropriate behavior
of the whole feature, the controller also need custom test cases.

### 4.2) Frontend

The frontend has a test suite based on Mocha with the BDD style API.
Every module requires its own testcase to ensure the behavior of the module.
Components should also mock the whole app lifecycle to test the communication.

5) Migrations
-------------

When the first release (*0.1.0*) is tagged, every model change requires its own migration file as the schema updating process of doctrine is not safe enough
and can lead to data loss.

This migration file can be generated using `bin/console doctrine:migrations:generate`.
