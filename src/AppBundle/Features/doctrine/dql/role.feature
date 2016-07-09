@user @role @repository
Feature: role repository
    Extended role model logic

    Scenario: determine default role
        When I determine the default role
        Then I should get the "ROLE_USER" role

    Scenario: missing default
        Given the database is purged
        When I determine the default role
        Then I should get an error
