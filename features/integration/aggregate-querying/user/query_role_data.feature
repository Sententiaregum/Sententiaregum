@aggregate-querying
Feature: query role data
    In order to establish a role-system inside the authorization process,
    certain query APIs for the role model are needed.

    Scenario: determine default role
        When I determine the default role
        Then I should get the "ROLE_USER" role

    Scenario: missing default
        Given the database is purged
        When I determine the default role
        Then I should get an error
