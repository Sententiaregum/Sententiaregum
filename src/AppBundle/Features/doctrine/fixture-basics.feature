@fixtures
Feature: fixture loading basics
    Implementation for applying a special list of fixtures

    Background:
        Given the database is purged

    Scenario: it applies fixture
        When I apply fixtures
        Then I should be able to fetch them from the database

    Scenario: it calls logging functions during appliance
        When I have a logging callback defined
        And I apply fixtures
        Then the callback should be called
        And the log messages should be shown

    Scenario: applying an invalid fixture
        When I apply an invalid fixture
        Then I should get an error

    Scenario: fetching fixtures from a directory
        When I load production fixtures from the DataFixtures/ORM directory inside AppBundle
        Then I should see the following fixtures
            | class                                   |
            | AppBundle\DataFixtures\ORM\AdminFixture |
            | AppBundle\DataFixtures\ORM\RoleFixture  |
