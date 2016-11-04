@database
Feature: production fixtures loader
    Fixtures are necessary in order to simulate certain workflows in a test/dev environment,
    but on a prod environment these fixtures shouldn't be present anymore. However some
    fixtures with very basic data are necessary to be applied there.

    Scenario: load production fixtures
        Given the database is purged
        When I run the production fixtures loader
        Then I should see the logging messages
        And the role and admin fixtures should be applied
