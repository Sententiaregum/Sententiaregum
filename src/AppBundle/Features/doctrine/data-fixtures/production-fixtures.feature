@fixtures @loader
Feature: production fixtures loader
    Some fixtures should be loaded on production such as the role fixture or the admin fixture.

    Scenario: load production fixtures
        Given the database is purged
        When I run the production fixtures loader
        Then I should see the logging messages
        And the role and admin fixtures should be applied
